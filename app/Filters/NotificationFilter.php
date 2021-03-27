<?php

namespace App\Filters;

use App\Models\Notification;    

class NotificationFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            // 'processed',
        ];

        if (in_array($filter, $data)) {
            $this->builder->where($filter, 'LIKE', "%{$value}%");
        } else {
            $this->builder->where($filter, $value);
        }

        return $this->builder;
    }

    public function type($value)
    {
        if (is_array($value)) {
        } else {
            $value = [$value];
        }

        if (in_array(Notification::TYPE_CANCEL_SUBSCRIPTION, $value)) {
            $this->builder->orderBy('created_at', 'desc');
        }

        if (in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $value) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $value)) {
            $this->builder->orderBy('data->paided_at', 'desc');
        }

        if (in_array(Notification::TYPE_ENDED_TRIAL_PERIOD, $value) || in_array(Notification::TYPE_ENDED_SUBSCRIPTIONS_DT, $value) || in_array(Notification::TYPE_ENDED_SUBSCRIPTIONS_DT_3, $value)) {
            $this->builder->join('subscriptions', 'subscriptions.id', '=', 'notifications.subscription_id')
            ->orderByRaw('CASE WHEN subscriptions.tries_at > subscriptions.ended_at THEN subscriptions.tries_at ELSE subscriptions.ended_at END ASC')
            ->select('notifications.*', 'subscriptions.tries_at', 'subscriptions.ended_at');
        }

        return $this->builder->whereIn('type', $value);
    }

    public function page($value)
    {
        
    }
}
