@php
$customBodyCode = setting_string('custom_body_code');
$customFooterCode = setting_string('custom_footer_code');
@endphp

{{-- Custom Body Code (inject after <body>) --}}
@if($customBodyCode)
{!! $customBodyCode !!}
@endif

{{-- Custom Footer Code (inject before </body>) --}}
@if($customFooterCode)
{!! $customFooterCode !!}
@endif
