<div class="pure-menu pure-menu-horizontal">
    {{--<a href="" class="pure-menu-heading pure-menu-link">Main Menu</a>--}}
    <span>Options:</span>
    <ul class="pure-menu-list">
        {{--<li class="pure-menu-item"><a id="reload" href="{{route('parseGetPDF')}}" class="pure-menu-link">Parse PDF to Aqb</a></li>--}}
        {{--<li class="pure-menu-item"><span>|</span></li>--}}
        <li class="pure-menu-item">
            <small><a id="reload" href="{{route('retrieveGetNORDPDF')}}" class="pure-menu-link" style="">Retrieve Packing Lists</a></small>
        </li> <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item">
            <small><a id="reload" href="{{route('parseGetNORDPDF')}}" class="pure-menu-link" style="">Add Packing Lists to DB</a></small>
        </li> <li class="pure-menu-item">
            <span>|</span></li>
        {{--<li class="pure-menu-item">--}}
        {{--<small><a id="" href="{{route('Getcheckforground')}}" class="pure-menu-link" style="">Check for Non-Ground NORD POs</a></small>--}}
        {{--</li>--}}
        {{--<span>|</span></li>--}}
        <li class="pure-menu-item">
            <small><a id="" href="{{route('deleteNORDDBForm')}}" class="pure-menu-link" style="">Reset NORDlatable DB</a></small>
        </li>




    </ul>
</div>