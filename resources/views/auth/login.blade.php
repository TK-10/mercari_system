<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TAKE商品管理システム</title>

    {{-- plugins:css --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    {{-- endinject --}}

    {{-- Layout styles --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    {{-- End layout styles --}}

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo text-center">
                  <img src="{{ asset('assets/images/logo2.png') }}" alt="logo">
                </div>

                <br>
                <form class="new_user" id="new_user" action="{{ route('login') }}" method="POST" accept-charset="UTF-8">
                  @csrf
                  <div class="form-group">
                    <label for="user_email">メールアドレス</label><br>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                      <span class="help-block text-danger">
                        <strong>{{ $errors->first('email') }}</strong>
                      </span>
                    @endif
                  </div>

                  <div class="form-group">
                    <label for="user_password">パスワード</label>
                    <input id="password" type="password" class="form-control" name="password">
                    @if ($errors->has('password'))
                      <span class="help-block text-danger">
                        <strong>{{ $errors->first('password') }}</strong>
                      </span>
                    @endif
                  </div>

                  <div class="mt-3">
                    <input type="submit" value="SIGN IN" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                  </div>

                  @if (config('auth.allow_guest_login', true))
                    <br><br>
                    <a href="{{ route('login.guest') }}" class="text-red">
                      <p style="text-align:center">ゲストログイン</p>
                    </a>
                  @endif
                </form>
              </div>
            </div>
          </div>
        </div>
        {{-- content-wrapper ends --}}
      </div>
      {{-- page-body-wrapper ends --}}
    </div>
    {{-- container-scroller --}}

    {{-- plugins:js --}}
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    {{-- endinject --}}

    {{-- inject:js --}}
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    {{-- endinject --}}
  </body>
</html>
