<?php

namespace App\Traits;

trait HasCrudAlerts
{
    /**
     * Flash a success alert for create operation
     */
    protected function alertCreated(string $resourceName, ?string $details = null): void
    {
        $this->alertSuccess("Thêm {$resourceName} thành công!", $details);
    }

    /**
     * Flash a generic success alert
     */
    protected function alertSuccess(string $message, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'success',
            'message' => $message,
            'details' => $details,
        ]);
    }

    /**
     * Flash a success alert for update operation
     */
    protected function alertUpdated(string $resourceName, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'success',
            'message' => "Cập nhật {$resourceName} thành công!",
            'details' => $details,
        ]);
    }

    /**
     * Flash a success alert for delete operation
     */
    protected function alertDeleted(string $resourceName, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'success',
            'message' => "Xóa {$resourceName} thành công!",
            'details' => $details,
        ]);
    }

    /**
     * Flash an error alert
     */
    protected function alertError(string $message, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'error',
            'message' => $message,
            'details' => $details,
        ]);
    }

    /**
     * Flash a warning alert
     */
    protected function alertWarning(string $message, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'warning',
            'message' => $message,
            'details' => $details,
        ]);
    }

    /**
     * Flash an info alert
     */
    protected function alertInfo(string $message, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => 'info',
            'message' => $message,
            'details' => $details,
        ]);
    }

    /**
     * Flash a custom alert
     */
    protected function alert(string $type, string $message, ?string $details = null): void
    {
        session()->flash('alert', [
            'type' => $type,
            'message' => $message,
            'details' => $details,
        ]);
    }
}
