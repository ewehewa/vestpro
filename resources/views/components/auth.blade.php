<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auth | TrustNex</title>
  <!-- CSS Links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  
  <style>
    [data-bs-theme=dark] {
      --brand: #34E834;
      --brand-hover: #36F936;
      --bs-body-bg: #000000;
      --bs-body-color: rgba(255, 255, 255, 0.85);
      --bs-secondary-color: rgba(255, 255, 255, 0.65);
      --bs-border-color: #2A2A2A;
      --bs-link-color: #34E834;
      --bs-link-color-rgb: 52, 232, 52;
      --bs-link-hover-color: #36F936;
      --bs-link-hover-color-rgb: 54, 249, 54;
    }
    body {
      background: #000000;
      font-family: 'Segoe UI', sans-serif;
    }
    .signup-card {
      max-width: 500px;
      margin: 50px auto;
      background-color: #111111;
      border: 1px solid #1F1F1F;
      box-shadow: 0 0 40px rgba(52, 232, 52, 0.08);
      border-radius: 15px;
    }
    /* Logo artwork is purple; render it white on the dark card */
    .signup-card img {
      filter: brightness(0) invert(1);
    }
    .brand-name {
      font-weight: bold;
      color: var(--brand);
    }
    .form-control,
    .form-select {
      background-color: #1A1A1A;
      border-color: #2A2A2A;
      color: #FFFFFF;
    }
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }
    .form-control:focus, .form-select:focus {
      background-color: #1A1A1A;
      color: #FFFFFF;
      border-color: var(--brand);
      box-shadow: 0 0 0 0.25rem rgba(52, 232, 52, 0.25);
    }
    .form-check-input:checked {
      background-color: var(--brand);
      border-color: var(--brand);
    }
    .form-check-input:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 0.25rem rgba(52, 232, 52, 0.25);
    }

    /* Primary button (class name kept from the old purple theme; page scripts reference it) */
    .btn-purple {
      background-color: var(--brand);
      border-color: var(--brand);
      color: #000000;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-purple:hover,
    .btn-purple:focus,
    .btn-purple:active,
    .btn-purple:disabled {
      background-color: var(--brand-hover) !important;
      border-color: var(--brand-hover) !important;
      color: #000000 !important;
    }

    .input-group-text {
      background-color: #1F1F1F;
      border-color: #2A2A2A;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
    }
    select.form-select {
      border-radius: 10px;
      padding: 0.6rem;
    }
  </style>
</head>
<body>

  <div class="container py-5">
    {{ $slot }}
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  
  <script>
    // Initialize Toastr with default options
    toastr.options = {
      "closeButton": true,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": true,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000"
    };
  </script>
  
  <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>