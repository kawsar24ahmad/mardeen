<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $orderNumber = '';
    public ?Order $order = null;
    public bool $searched = false;
    public ?string $notFoundMessage = null;

    /**
     * Status pipeline used to render the stepper.
     * 'cancelled' is handled as a special "out of band" state in the view.
     */
    public array $statuses = [
        'pending'    => 'Pending',
        'processing' => 'Processing',
        'shipped'    => 'Shipped',
        'delivered'  => 'Delivered',
    ];

    protected $rules = [
        'orderNumber' => 'required|string|min:3|max:100',
    ];

    protected $messages = [
        'orderNumber.required' => 'Please enter an order number or tracking code.',
        'orderNumber.min'      => 'Order number must be at least 3 characters.',
    ];

    /**
     * Look up the order using either the order_number or the tracking_code / tracking_number column.
     */
    public function track(): void
    {
        $this->validate();
        $this->resetResults();

        $needle = trim($this->orderNumber);

        $order = Order::query()
            ->with([
                'items',
                'orderStatuses' => fn ($q) => $q->orderBy('created_at', 'desc'),
                'courier',
                'customer',
            ])
            ->where(function ($q) use ($needle) {
                $q->where('order_number', $needle)
                    ->orWhere('tracking_code', $needle)
                    ->orWhere('tracking_number', $needle)
                    ->orWhere('consignment_id', $needle);
            })
            ->first();

        if ($order) {
            $this->order = $order;
        } else {
            $this->notFoundMessage = "We couldn't find any order matching \"{$needle}\". Please double-check and try again.";
        }

        $this->searched = true;
    }

    /**
     * Clear the current result so the user can search again.
     */
    public function resetSearch(): void
    {
        $this->orderNumber     = '';
        $this->resetResults();
    }

    protected function resetResults(): void
    {
        $this->order           = null;
        $this->searched        = false;
        $this->notFoundMessage = null;
    }

    /**
     * Returns the 0-based index of the current status inside the stepper pipeline.
     * Unknown statuses fall back to "pending" (0).
     */
    public function currentStatusIndex(): int
    {
        if (! $this->order) {
            return -1;
        }

        $key = strtolower((string) $this->order->status);

        if ($key === 'cancelled') {
            return -1; // handled separately in the view
        }

        $index = array_search($key, array_keys($this->statuses), true);

        return $index === false ? 0 : $index;
    }

    public function isCancelled(): bool
    {
        return $this->order && strtolower((string) $this->order->status) === 'cancelled';
    }

    /**
     * Human-friendly badge color for a given status.
     */
    public function statusColor(string $status): string
    {
        return match (strtolower($status)) {
            'pending'             => 'warning',
            'processing'          => 'info',
            'shipped'             => 'primary',
            'delivered'           => 'success',
            'cancelled'           => 'danger',
            'returned', 'refunded'=> 'secondary',
            default               => 'secondary',
        };
    }

    public function render()
    {
        return view('livewire.track-order')->layout('components.layouts.frontend');
    }
}
