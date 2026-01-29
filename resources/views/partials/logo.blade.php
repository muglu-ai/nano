<style>
.logo-container {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: center;
    background-color: white;
    gap: 24px;
    padding: 10px 0;
}
.logo-container img {
    max-width: 180px;
    width: 100%;
    height: auto;
    /*object-fit: contain;*/
    transition: transform 0.2s;
}
.logo-container img:hover {
    transform: scale(1.05);
}
@media (max-width: 600px) {
    .logo-container {
        gap: 12px;
        padding: 8px 0;
    }
    .logo-container img {
        max-width: 90px;
    }
}
</style>

<div class="logo-container">
    <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.event_logo') }}">
{{--    <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}" alt="SEMI IESA Logo">--}}
{{--    <img src="{{ asset('asset/img/logos/meity-logo.png') }}" alt="MeitY Logo">--}}
{{--    <img src="{{ asset('asset/img/logos/ism_logo.png') }}" alt="ISM Logo">--}}
{{--    <img  src="{{ asset('asset/img/logos/DIC_Logo.webp') }}" alt="Digital India Logo">--}}
</div>