<form action="{{ route('checkout') }}" method="POST">
    @csrf
    <input type="number" name="amount" value="2.00" step="0.01" min="2.00" required>
    <button type="submit">CheckOut</button>
</form>

@if ($errors->any())
    <div style="color: red;">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif