@include('layouts.header', ['title' => 'Edit report Report - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit Promotor Report</h1>

    <div id="report"> 

        <style>
            .chosen-container.chosen-container-single {
                width: 100% !important; /* or any value that fits your needs */
            }
        </style>


        @if(Session::has('report-updated'))
            <div class="note success">Data has been updated.</div>
        @endif    
        
        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/report/edit">
            {!! csrf_field() !!}
            
            <input type="hidden" name="ID" value="{{ $report->ID }}" />

            <div class="form-group">
                <label for="product">Product</label>
                {!! Form::select('type', ['source' => 'Source', 'custom' => 'Custom'], $type, ['class' => 'form-control product_type']) !!}
            </div>

            <div class="form-group report-field custom hide">
                <label for="product">Product</label>
                <input type="text" class="form-control" name="custom_name" id="custom_name" placeholder="Product" value="{{ $report->custom_name }}" />    
            </div>

            <div class="form-group report-field custom hide">
                <label for="product">Price</label>
                <input type="text" class="form-control" name="price" id="price" placeholder="Product price" value="{{ $report->price }}" />
            </div>

            <div class="form-group report-field source hide">
                <label for="product">Product</label>
                {!! Form::select('product_model_ID', $products, $report->product_model_ID, ['class' => 'form-control chosen-select']) !!}
            </div>

            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="text" class="form-control" name="quantity" id="quantity" placeholder="Report quantity" value="{{ $report->quantity }}">
            </div>

            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" class="form-control datepicker pointer" readonly name="date" id="date" value="{{ date($report->date) }}" placeholder="Date when the report published">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/report/view?ID={{ $report->promotor_ID }}&date={{ $date }}" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
