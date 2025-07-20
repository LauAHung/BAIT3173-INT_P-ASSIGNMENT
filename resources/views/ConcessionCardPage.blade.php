@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
    <link href="css/ConcessionCard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
    <main>
        <section class="first-section">
            <div class="preface">
                <h1>
                    Special Benefit
                </h1><br><br><br>
                <p>
                    At Travel Free, we believe that travel should be accessible, affordable, and inclusive for everyone. 
                    Our Concession Card system is designed to make train journeys more affordable for students, 
                    senior citizens, persons with disabilities, and other eligible groups across the nation.
                </p><br><br>
                <p>
                    This webpage is your gateway to apply for, renew, or manage your Travel Free Concession Card. 
                    Whether you're commuting daily for school, work, or leisure, our aim is to provide a seamless ticketing 
                    experience with special discounts and privileges for those who need it most.
                </p>
            </div>
            <div class="img_preface">
                <img src="../images/concession_card.png">
            </div>
        </section>

        <section class="second-section">
            <
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/HomePage.js') }}" defer></script>
@endsection