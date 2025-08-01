<form action="{{ route('checkout') }}" method="POST">
    @csrf
    <button type="submit">CheckOut</button>
</form>