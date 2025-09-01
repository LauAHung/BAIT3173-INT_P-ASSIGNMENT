@if ($errors->any())
    <div id="error-modal-overlay" style="position:fixed; inset:0; z-index:2000; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.5);">
        <div style="background:#ffffff; border-radius:12px; padding:20px; width:90%; max-width:420px; text-align:center; position:relative; box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <h2 style="color:#e11d48; font-size:18px; margin-bottom:12px;">Error Occurred</h2>
            <img src="{{ asset('images/icon/error.gif') }}" alt="Error GIF" style="width:160px; height:100px; object-fit:contain; margin:0 auto 12px; display:block;">
            <div style="color:#333; font-size:14px; text-align:left; max-height:160px; overflow:auto; margin-bottom:14px;">
                @foreach ($errors->all() as $error)
                    <div>- {{ $error }}</div>
                @endforeach
            </div>
            <button onclick="(function(){var m=document.getElementById('error-modal-overlay'); if(m){m.style.display='none';}})()" style="padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:8px; cursor:pointer;">Back</button>
        </div>
    </div>
@endif
