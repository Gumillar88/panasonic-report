@include('layouts.header', ['title' => 'Remove Promotor - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Promotor</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/promotor/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the promotor?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/promotor" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
