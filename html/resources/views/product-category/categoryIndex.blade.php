@include('layouts.header', ['title' => 'Product Category - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Product Category</h1>

    @if(Session::has('category-created'))
        <div class="note success">Product category has been created</div>
    @endif

    @if(Session::has('category-deleted'))
        <div class="note error">Product category has been removed</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/product/category/create" class="btn btn-green">Create</a>
        <a href="{{ env('APP_HOME_URL') }}/product/model/upload" class="btn btn-green">Import From Excel</a>
    </div>

    <table id="userTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No</th>
                <th data-width="45%">Name</th>
                <th data-width="40%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $key => $category)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/product/model/?category_ID={{ $category->ID }}" class="btn btn-green">View</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/category/edit?ID={{ $category->ID }}" class="btn btn-green">Rename</a>
                        <a href="{{ env('APP_HOME_URL') }}/product/category/remove?ID={{ $category->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
