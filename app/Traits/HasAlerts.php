<?php

namespace App\Traits;

trait HasAlerts
{
    protected function alertSuccess($message)
    {
        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    protected function alertError($message)
    {
        return redirect()->back()->with('alert', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    protected function alertInfo($message)
    {
        return redirect()->back()->with('alert', [
            'type' => 'info',
            'message' => $message,
        ]);
    }

    protected function alertWarning($message)
    {
        return redirect()->back()->with('alert', [
            'type' => 'warning',
            'message' => $message,
        ]);
    }
}
