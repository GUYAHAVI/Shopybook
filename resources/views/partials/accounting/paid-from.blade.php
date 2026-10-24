{{-- "Paid from" money account picker. Expects $moneyAccounts; optional $selected, $name, $label, $help. --}}
@php($fieldName = $name ?? 'paid_from_account_id')
<div class="mb-3">
    <label for="{{ $fieldName }}" class="form-label">{{ $label ?? 'Paid from' }}</label>
    <select class="form-control" id="{{ $fieldName }}" name="{{ $fieldName }}">
        @foreach($moneyAccounts as $account)
            <option value="{{ $account->id }}" @selected((string) old($fieldName, $selected ?? '') === (string) $account->id || (!old($fieldName, $selected ?? null) && $account->system_key === 'cash'))>{{ $account->name }}</option>
        @endforeach
    </select>
    <div class="form-text">{{ $help ?? 'Where the money came out of. This keeps your cash, M-Pesa and bank balances correct.' }}</div>
    @error($fieldName)<div class="text-danger small">{{ $message }}</div>@enderror
</div>
