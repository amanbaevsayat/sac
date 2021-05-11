<?php

namespace App\Repositories;

use App\Models\Subscription;

class FaqRepositoryEloquent // implements FaqRepository
{
    protected $subscriptionModel;

    public function __construct(Subscription $subscription)
    {
        $this->subscriptionModel = $subscription;
    }

    public function newInstance(array $attributes = array())
    {
        // if (!isset($attributes['rank'])) {
        //     $attributes['rank'] = 0;
        // }

        return $this->subscriptionModel->newInstance($attributes);
    }

    public function create(array $attributes)
    {
        return $this->subscriptionModel->create($attributes);
    }

    public function find($id, $columns = array('*'))
    {
        return $this->subscriptionModel->findOrFail($id, $columns);
    }

    public function updateWithIdAndInput($id, array $input)
    {
        $faq = $this->subscriptionModel->find($id);
        return $faq->update($input);
    }

    public function destroy($id)
    {
        return $this->subscriptionModel->destroy($id);
    }
}
