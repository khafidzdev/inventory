@extends('templates.auth')
@section('title', 'Login')
@section('content')
<form class="col-lg-3 col-md-4 col-10 mx-auto text-center" method="POST" action="{{ route('login.post') }}">
  @csrf
  <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ url('/') }}">
    <svg version="1.1" id="logo" class="navbar-brand-img brand-md" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
      <g>
        <polygon class="st0" points="78,105 15,105 24,87 87,87 	" />
        <polygon class="st0" points="96,69 33,69 42,51 105,51 	" />
        <polygon class="st0" points="78,33 15,33 24,15 87,15 	" />
      </g>
    </svg>
  </a>
  <h1 class="h6 mb-3">Login</h1>

  @if ($errors->any())
    <div class="alert alert-danger text-left small py-2 px-3 mb-3" role="alert">
      <ul class="mb-0 pl-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('status'))
    <div class="alert alert-success text-left small py-2 px-3 mb-3" role="alert">
      {{ session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger text-left small py-2 px-3 mb-3" role="alert">
      {{ session('error') }}
    </div>
  @endif

  <div class="form-group text-left">
    <label for="inputEmail" class="sr-only">Email</label>
    <input type="email" name="email" id="inputEmail" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
    @error('email')
      <span class="text-danger small mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="form-group text-left">
    <label for="inputPassword" class="sr-only">Password</label>
    <div class="input-group">
      <input type="password" name="password" id="inputPassword" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required style="border-right: 0;">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary bg-transparent border-left-0" type="button" id="togglePassword" style="border-color: #ced4da;" aria-label="Toggle password visibility">
          <i class="fe fe-eye fe-16 text-muted" id="togglePasswordIcon"></i>
        </button>
      </div>
    </div>
    @error('password')
      <span class="text-danger small mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="checkbox mb-3 text-left">
    <label>
      <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Ingat saya
    </label>
  </div>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
  <p class="mt-3 text-muted">© {{ date('Y') }} Inventory</p>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('togglePassword');
    var passwordInput = document.getElementById('inputPassword');
    var icon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && passwordInput && icon) {
      toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('fe-eye');
          icon.classList.add('fe-eye-off');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('fe-eye-off');
          icon.classList.add('fe-eye');
        }
      });
    }
  });
</script>
@endsection
