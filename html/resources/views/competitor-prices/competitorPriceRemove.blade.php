@include('layouts.header', ['title' => 'Remove Competitor Price - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Competitor Price</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/competitor-price/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $price->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the competitor price data?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/competitor-price" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
