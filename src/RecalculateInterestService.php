<?php

declare(strict_types=1);

namespace FinCore\Domain\Invoice\Service\InterestCalculation;

use Agreements\Entity\LoanItem;
use Applications\Entity\Application;
use Applications\Enum\ApplicationTypeEnum;
use Common\Model\TimePeriod;
use Common\Service\MoneyMathematics\MoneyMath;
use Common\Tool\NumberTool;
use FinCore\Domain\Calculator\Arithmetic\Service\ArithmeticResolver\ArithmeticResolverInterface;
use FinCore\Domain\Calculator\Payload\ValueObject\CalculatorConstructorPayload;
use FinCore\Domain\FinanceService\ValueObject\FinanceServiceModel;
use FinCore\Domain\Invoice\Service\InterestCalculationChecker\InterestCalculationCheckerServiceInterface;
use FinCore\Domain\Loan\ValueObject\LoanAmountModel;
use FinCore\Domain\LoanItem\Service\CalculateLoanItem\CalculateLoanItemServiceInterface;
use Invoices\Entity\Invoice;
use SystemSettings\Interfaces\SettingsReaderInterface;

class RecalculateInterestService implements RecalculateInterestServiceInterface
{
    /**
     * @var ArithmeticResolverInterface
     */
    private $arithmeticResolver;

    /**
     * @var SettingsReaderInterface
     */
    private $settingsReader;

    /**
     * @var InterestCalculationCheckerServiceInterface
     */
    private $checkerService;

    /**
     * @var CalculateLoanItemServiceInterface
     */
    private $loanItemService;

    public function __construct(
        ArithmeticResolverInterface $arithmeticResolver,
        SettingsReaderInterface $settingsReader,
        InterestCalculationCheckerServiceInterface $checkerService,
        CalculateLoanItemServiceInterface $loanItemService
    ) {
        $this->arithmeticResolver = $arithmeticResolver;
        $this->settingsReader = $settingsReader;
        $this->checkerService = $checkerService;
        $this->loanItemService = $loanItemService;
    }

    /**
     * @param Invoice $invoice
     * @param $overdueDays
     *
     * @throws \Common\Service\MoneyMathematics\MathOperationException
     *
     * @return bool
     */
    public function recalculate(Invoice $invoice, $overdueDays): bool
    {
        /** @var Invoice $invoice */
        $loan = $invoice->getLoan();

        if (! $this->checkerService->isCalculationAllowed($invoice, $overdueDays)) {
            return false;
        }
        $disableLoanZeroInterestSetting = $this
            ->settingsReader
            ->get('financial_services.loan_zero_interest_disable_after_duedays');

        $disableLoanZeroInterest = ($disableLoanZeroInterestSetting > 0 && $overdueDays >= $disableLoanZeroInterestSetting + 1);

        if (! $disableLoanZeroInterest) {
            return false;
        }

        $finVersionNum = $loan->getFinancialServiceVersion()->getVersion();
        $loanPaidPrincipal = new MoneyMath($invoice->getLoan()->getPaidPrincipalAmount());

        $loanAmountModel = new LoanAmountModel($loan);

        /** @var LoanItem $loanItem */
        foreach ($loan->getLoanItems() as $loanItem) {
            if ($loanPaidPrincipal->isGreaterThenOrEqualTo($loanItem->getPrincipalAmount())) {
                $loanPaidPrincipal->sub($loanItem->getPrincipalAmount());

                continue;
            }

            /** @var Application $application */
            $application = $loanItem->getApplication();

            $currentLoanItemPrincipal = new MoneyMath($loanItem->getPrincipalAmount());

            if ($this->checkerService->isPrincipalDecreaseAllowed($loanPaidPrincipal)) {
                $currentLoanItemPrincipal->sub($loanPaidPrincipal->getResult());
                $loanPaidPrincipal->zero();
            }

            $applicationTypeEnum = ($application->isItemTypeAdditional()
                ? ApplicationTypeEnum::ADDITIONAL()
                : ApplicationTypeEnum::MAIN());

            // TODO TD lesser evil
            $payload = new CalculatorConstructorPayload(
                new FinanceServiceModel($application->getServiceTypeEnum(), $finVersionNum),
                $currentLoanItemPrincipal->getResult(),
                $application->getAppDate(),
                $application->getTerm(), // TODO KZ + dueDays
                // $term, // TODO KZ + dueDays
                /*
                 * PL 30
                 * KZ 30 +1 (daily 30)
                 */
                $applicationTypeEnum
            );

            $payload
                ->setIsDiscountEnabled(false)
                ->setUseRecalculation(true)
            ;

            $calcResult = $this->arithmeticResolver->calculate($payload);

            // TODO TD lesser evil
            if (($invoice->getDueDays() < 0) && 'kz' == getenv('COUNTRY_ENV')) {
                $days = (int) NumberTool::add($application->getTerm()->getValue(), abs($invoice->getDueDays()));
                if ($days > 60) {
                    $days = 60;
                }

                $term = TimePeriod::fromDays($days);

                // TODO TD lesser evil
                $fakePayload = new CalculatorConstructorPayload(
                    new FinanceServiceModel($application->getServiceTypeEnum(), $finVersionNum),
                    $currentUnpaidLoanItemPrincipal->getResult(),
                    $application->getAppDate(),
                    $term, // TODO KZ + dueDays
                    // $application->getTerm(), // TODO KZ + dueDays
                    /*
                     * PL 30
                     * KZ 30 +1 (daily 30)
                     */
                    $applicationTypeEnum
                );

                $fakePayload
                    ->setIsDiscountEnabled(false)
                    ->setUseRecalculation(true)
                ;

                $fakeCalcResult = $this->arithmeticResolver->calculate($fakePayload);

                $calcResult->setInterestAmount($fakeCalcResult->getInterestAmount());
                $calcResult->setInterestRate($fakeCalcResult->getInterestRate());
            }

            $result = $this->loanItemService->calculate($loanItem, $calcResult, $loanAmountModel);

            if (getenv('COUNTRY_ENV')) {
                $maxOverall = NumberTool::percent($loan->getPrincipalAmount()->getAmount(), 100);
                $overallAmount = NumberTool::addAll(
                    $result->getServiceFeeAmount()->getAmount(),
                    $result->getInterestAmount()->getAmount(),
                    $loan->getRemindersAmount()->getAmount(),
                    $loan->getFinesAmount()->getAmount()
                );

                if (NumberTool::gt($overallAmount, $maxOverall)) {
                    return false;
                }
            }

            $loanItem
                ->setServiceFeeAmount($result->getServiceFeeAmount())
                ->setInterestAmount($result->getInterestAmount())
                ->setTotalRepayableAmount($result->getTotalRepayableAmount())
                ->setPeriodEndRepayableAmount($result->getTotalRepayableAmount())
            ;
        }

        $invoice->setHasRecalculateInterest(true);

        $loan->doUpdateAmountsFromLoanItems();

        return true;
    }
}
