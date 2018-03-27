
<div id="navbar">
    <span id="expandNavbar"><i class="fa fa-bars"></i>Menu</span>
    <ul>
        <li>
            <a href="{{ env('APP_HOME_URL') }}">Home</a>
        </li>
		<li class="expandNavbarItem">
            <a>Data <i class="fa fa-plus"></i></a>
            <ul>
                <li>
					<a href="{{ env('APP_HOME_URL') }}/region">Regions</a>
				</li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/branch">Branches</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/dealer-account">Dealer Account</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/dealer">Dealers</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/dealer-type">Dealer Types</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/dealer-channel">Dealer Channels</a>
                </li>
				<li>
					<a href="{{ env('APP_HOME_URL') }}/promotor">Promotors</a>
				</li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/report">Promotor Reports</a>
                </li>
				<li>
					<a href="{{ env('APP_HOME_URL') }}/sales-target">Sales Target</a>
				</li>
            </ul>
        </li>
        <li class="expandNavbarItem">
            <a>Products <i class="fa fa-plus"></i></a>
            <ul>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/product/category">Data Products</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/product/price">Price Product</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/product/incentive">Product Incentives</a>
                </li>
            </ul>
        </li>
        <li>
			<a href="{{ env('APP_HOME_URL') }}/dashboard-account">Dashboard Accounts</a>
		</li>
        <li class="expandNavbarItem">
            <a>Competitor Data<i class="fa fa-plus"></i></a>
            <ul>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/competitor-brand">Brands</a>
                </li>
                <li>
                    <a href="{{ env('APP_HOME_URL') }}/competitor-price">Price Data</a>
                </li>
            </ul>
        </li>
        <li>
			<a href="{{ env('APP_HOME_URL') }}/news">News</a>
		</li>
        @if(Session::get('user_status') === 'admin')
            <li>
                <a href="{{ env('APP_HOME_URL') }}/user">Users</a>
            </li>
        @endif
        
        <li>
            <a href="{{ env('APP_HOME_URL') }}/logout">Logout</a>
        </li>
    </ul>
</div>
