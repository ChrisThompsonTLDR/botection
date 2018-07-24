@if (session('success')
     || session('danger')
     || session('error')
     || session('status')
     || (isset($errors) && count($errors) > 0))
<div class="container">
    <div class="row mb-3">
        <div class="col">
        @if (session('success'))
            <div class="alert alert-success">
                {!! session('success') !!}
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">
                {!! session('status') !!}
            </div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger">
                {!! session('danger') !!}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {!! session('error') !!}
            </div>
        @endif
        @if (isset($errors) && count($errors) > 0)
            <div class="alert alert-danger">
                <ul class="fa-ul">
                    @foreach ($errors->all() as $error)
                        <li><span class="fa-li"><i class="fas fa-circle fa-xs"></i></span> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        </div>
    </div>
</div>
@endif