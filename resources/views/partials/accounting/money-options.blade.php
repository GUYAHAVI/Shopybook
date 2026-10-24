{{-- Money account <option>s. Expects $accounts; optional $name for old() key. --}}
@php($field = $name ?? 'from_account_id')
@foreach($accounts as $account)
    <option value="{{ $account->id }}" @selected((string) old($field) === (string) $account->id || (!old($field) && $account->system_key === 'cash'))>{{ $account->name }}</option>
@endforeach
