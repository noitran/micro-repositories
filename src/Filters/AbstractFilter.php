<?php

namespace Noitran\Repositories\Filters;

use Noitran\Repositories\Contracts\Filter\FilterInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Noitran\Repositories\Criteria\LimitBy;
use Noitran\Repositories\Repositories\AbstractRepository;
use Noitran\Repositories\Requests\InteractsWithRequest;

/**
 * Class AbstractFilter
 */
abstract class AbstractFilter implements FilterInterface
{
    use InteractsWithRequest;

    /**
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $queryFilters = [];

    /**
     * @var array
     */
    protected $querySettings = [];

    /**
     * AbstractFilter constructor.
     */
    public function __construct()
    {
        $this->setQuerySettings();
    }

    /**
     * @param RepositoryInterface $repository
     *
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param array $queryFilters
     *
     * @return AbstractFilter
     */
    public function setQueryFilters(array $queryFilters = []): self
    {
        $defaultQueryFilters = config('repositories.filtering.default_filters', []);

        $this->queryFilters = array_merge($defaultQueryFilters, $queryFilters);

        return $this;
    }

    /**
     * @return array
     */
    public function getQueryFilters(): array
    {
        return $this->queryFilters;
    }

    /**
     * @param array $querySettings
     *
     * @return AbstractFilter
     */
    public function setQuerySettings(array $querySettings = []): self
    {
        $defaultQuerySettings = config('repositories.filtering.default_settings', []);
        $this->querySettings = array_merge($defaultQuerySettings, $querySettings);

        return $this;
    }

    /**
     * @return array
     */
    public function getQuerySettings(): array
    {
        return $this->querySettings;
    }

    /**
     * @param array $queryFilters
     * @param array $request
     *
     * @return array
     */
    public function getInput(array $queryFilters = [], array $request = []): array
    {
        $queryKeys = array_keys($this->getQuerySettings());

        $input = $this->only(
            array_merge($queryKeys, $this->getQueryParams($queryFilters)),
            $request
        );

        return array_merge(
            $this->getQuerySettings(),
            array_filter($input, function ($value) {
                return $value !== null;
            })
        );
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $input
     *
     * @return RepositoryInterface
     */
    public function pushFilters(RepositoryInterface $repository, array $input = []): RepositoryInterface
    {
        foreach ($this->getQueryFilters() as $filter) {
            if (isset($input[$filter['queryParameter']])) {
                $repository = $repository->pushCriteria(
                    new $filter['uses'](
                        $input[$filter['queryParameter']]
                    )
                );
            }
        }

        return $repository;
    }

    /**
     * @param int|null $limit
     *
     * @throws \Noitran\Repositories\Exceptions\RepositoryException
     *
     * @return AbstractFilter
     */
    public function limit(int $limit = null): self
    {
        $this->repository->pushCriteria(new LimitBy($limit));

        return $this;
    }

    /**
     * @param array|string $relations
     *
     * @return $this
     */
    public function setWith($relations): self
    {
        $this->repository->with($relations);

        return $this;
    }

    /**
     * @param array|string $relations
     *
     * @return $this
     */
    public function setWithCount($relations): self
    {
        $this->repository->withCount($relations);

        return $this;
    }

    /**
     * @throws \Noitran\Repositories\Exceptions\RepositoryException
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function all()
    {
        return $this->repository->all();
    }

    /**
     * @param int|null $perPage
     *
     * @throws \Noitran\Repositories\Exceptions\RepositoryException
     *
     * @return mixed
     */
    public function paginate(int $perPage = null)
    {
        if (! $perPage) {
            $perPage = config('repositories.filtering.default_settings.per_page');
        }

        return $this->repository->paginate($perPage);
    }
}
