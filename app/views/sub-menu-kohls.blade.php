<div class="pure-menu pure-menu-horizontal">
    {{--<a href="" class="pure-menu-heading pure-menu-link">Main Menu</a>--}}
    <span>Options:</span>
    <ul class="pure-menu-list">
        <li class="pure-menu-item"><a id="reload" href="{{route('parseGetPDF')}}" class="pure-menu-link">Parse PDF to Aqb</a></li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item">
            <small><a id="reload" href="{{route('retrieveGetKohlsPDF')}}" class="pure-menu-link" style="">Retrieve Packing Lists by PO(s)</a></small>
        </li> <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item">
            <small><a id="reload" href="{{route('parseGetKohlsPDF')}}" class="pure-menu-link" style="">Add Packing Lists to DB</a></small>
        </li> <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item">
            <small><a id="" href="{{route('deleteKohlsDBForm')}}" class="pure-menu-link" style="">Reset Kohls DB</a></small>
        </li>




    </ul>
</div>