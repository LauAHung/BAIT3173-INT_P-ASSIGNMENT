@extends('Layout.master')

@section('title', 'Access Denied')

@push('styles')
    <style>
        .access-denied-wrapper { 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 60vh; 
            padding: 24px; 
        }
        .access-denied-card { 
            display: grid; 
            grid-template-columns: 1.2fr 1fr; 
            align-items: center; 
            gap: 32px; 
            background: #ffffff; 
            border-radius: 12px; 
            box-shadow: 0 10px 24px rgba(0,0,0,0.08); 
            padding: 32px 40px; 
            max-width: 900px; 
            width: 100%; 
            margin-top: 100px;
            margin-bottom: 100px;
        }
        .access-denied-content h1 { 
            margin: 0 0 12px; 
            font-size: 28px; 
            color: #d44c4c; 
        }
        .access-denied-content p { 
            margin: 0 0 20px; 
            color: #555; 
        }
        .access-denied-actions .btn-back { 
            display: inline-block; 
            padding: 10px 16px; 
            border: 1px solid #cfcfcf; 
            border-radius: 6px; 
            background: #fff; 
            color: #333; 
            text-decoration: none; 
            transition: background .2s ease, border-color .2s ease; 
        }
        .access-denied-actions .btn-back:hover { 
            background: #f5f5f5; 
            border-color: #bbb; 
        }
        .access-denied-illustration { 
            text-align: center; 
        }
        .access-denied-illustration img { 
            max-width: 260px; 
            width: 100%; 
            height: auto; 
        }
        @media (max-width: 768px) {
            .access-denied-card { 
                grid-template-columns: 1fr; 
                text-align: center; 
            }
        }
    </style>
@endpush

@section('content')
    <div class="access-denied-wrapper">
        <div class="access-denied-card">
            <div class="access-denied-content">
                <h1>You don't have access to the page!</h1>
                <p>Only the authorized person can access the administrator page.</p>
                <div class="access-denied-actions">
                    <a class="btn-back" href="javascript:void(0)" onclick="window.history.back()">Go Back</a>
                </div>
            </div>
            <div class="access-denied-illustration">
                <img src="{{ asset('images/403.jpeg') }}" alt="403 Access Denied Illustration">
            </div>
        </div>
    </div>
@endsection


