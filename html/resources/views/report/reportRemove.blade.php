@include('layouts.header', ['title' => 'Remove Report - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Promotor Report</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/report/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $ID }}" />
            <input type="hidden" name="date" value="{{ $date }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the promotor report?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/report/view?ID={{ $promotorID }}&date={{ $date }}" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
