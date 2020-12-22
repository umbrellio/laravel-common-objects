<?php

declare(strict_types=1);

namespace Umbrellio\Common\Contracts;

use Hemp\Presenter\Presenter;
use Illuminate\Contracts\Queue\QueueableEntity;
use JsonSerializable;

class AbstractPresenter extends Presenter implements JsonSerializable, QueueableEntity
{
    private $relationMapping = [];

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __get($key)
    {
        if ($this->isRelationMutator($key)) {
            return $this->mutateRelation($key);
        }
        return parent::__get($key);
    }

    public function getQueueableId()
    {
        return $this->model->getQueueableId();
    }

    public function getQueueableRelations()
    {
        return $this->model->getQueueableRelations();
    }

    public function getQueueableConnection()
    {
        return $this->model->getQueueableConnection();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function relationPresenters(array $mapping): self
    {
        $this->relationMapping = $mapping;
        return $this;
    }

    private function isRelationMutator($key): bool
    {
        return array_key_exists($key, $this->relationMapping);
    }

    private function mutateRelation($key)
    {
        $presenterClass = $this->relationMapping[$key];
        $result = $this->model->{$key};
        if ($result === null) {
            return null;
        }
        return $result->present($presenterClass);
    }
}
