@include('layouts.header', ['title' => 'Upload Product - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Upload Product Price</h1>

    <div>
        @if($type === 'review')
        
            
            <h5>Review Data</h5>
            <p>Please review this data before being submitted to database. Make sure everything is right.</p>
            
            <div id="upload-meter" class="hide">
                <div class="meter-container">
                    <div class="meter-value"></div>
                </div>
                <p class="text-center">
                     (<span id="uploadCounter"></span>)
                </p>
            </div>
        
            <table class="display initTable">
                <thead>
                    <tr>
                        <th data-width="5%">No.</th>
                        <th data-width="35%">Product</th>
                        <th data-width="5%">Dealer Type</th>
                        <th>MUP</th>
                        <th>SO</th>
                        <th>SMO</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $key => $row)
                        <tr id="row-product-{{ $row['product'] }}" class="row-product queue">
                            <td>{{ $key + 1 }}</td>
                            <td class="cell cell-product" data-product="{{ $row['product'] }}">{{ $row['product'] }}</td>
                            <td class="cell cell-dealer_type" data-dealer_type="{{ $row['dealerType'] }}">{{ $row['dealerType'] }}</td>
                            <td class="cell cell-price_mup" data-price_mup="{{ $row['price_MUP'] }}">{{ $row['price_MUP'] }}</td>
                            <td class="cell cell-price_so" data-price_so="{{ $row['price_SO'] }}">{{ $row['price_SO'] }}</td>
                            <td class="cell cell-price_smo" data-price_smo="{{ $row['price_SMO'] }}">{{ $row['price_SMO'] }}</td>
                            <td class="cell cell-status"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
            <p class="text-center">
                <a id="uploadProductPrice" class="btn btn-green">Upload</a>
                <a id="uploadLoading" class="btn btn-green hide">Uploading...</a>
                <a href="{{ env('APP_HOME_URL') }}/product/price" id="uploadFinish" class="btn btn-green hide">Finish</a>
            </p>
        
        @else
            

            <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/product/price/upload" enctype="multipart/form-data">
                {!! csrf_field() !!}


                @if(count($errors) > 0)
                    <div class="note error">{{ $errors->all()[0] }}</div>
                @endif
                
                <div class="form-group">
                    <label for="excel">File</label>
                    <input type="file" class="form-control" name="excel" id="excel">
                    <p class="help-block">File format must be in Excel (.xlsx)</p>
                </div>
                
                <p class="text-center">
                    <input type="submit" class="btn btn-green" value="Create" />
                    <a href="{{ env('APP_HOME_URL') }}/product/price" class="btn">Back</a>
                </p>

            </form>
        
        @endif

    </div>

</div>

@include('layouts.footer');
