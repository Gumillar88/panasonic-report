@include('layouts.header', ['title' => 'Remove Dealer Channel - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Dealer Channel</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dealer-channel/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the dealer type ?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/dealer-channel" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
