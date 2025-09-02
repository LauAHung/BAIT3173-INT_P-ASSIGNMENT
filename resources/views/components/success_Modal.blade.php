@if (session('success'))
    <div id="success-modal-overlay" style="position:fixed; inset:0; z-index:2000; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.5);">
        <div style="background:#ffffff; border-radius:12px; padding:20px; width:90%; max-width:420px; text-align:center; position:relative; box-shadow:0 10px 30px rgba(0,0,0,0.4);">
            <h2 style="color:#10b981; font-size:18px; margin-bottom:12px;">Success</h2>
            <img src="{{ asset('images/icon/success.gif') }}" alt="Success GIF" style="width:160px; height:100px; object-fit:contain; margin:0 auto 12px; display:block;">
            <div style="color:#333; font-size:14px; margin-bottom:14px;">{{ session('success') }}</div>
            <button onclick="(function(){var m=document.getElementById('success-modal-overlay'); if(m){m.style.display='none';}})()" style="padding:8px 16px; background:#10b981; color:#fff; border:none; border-radius:8px; cursor:pointer;">Close</button>
        </div>
    </div>
@endif


