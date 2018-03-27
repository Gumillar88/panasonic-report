@include('layouts.header', ['title' => 'Remove Product Incentive - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Product Incentive</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/incentive/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $incentive->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove incentive for {{ $productModels[$incentive->product_model_ID] }} ({{ $dealerChannels[$incentive->dealer_channel_ID] }})?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/product/incentive" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
