@include('layouts.header', ['title' => 'Remove Competitor Brand - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Competitor Brand</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/competitor-brand/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $brand->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the brand "{{ $brand->name }}"?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/competitor-brand" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
