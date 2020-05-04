<div class="pure-menu pure-menu-horizontal" style="margin-top:20px;">
    {{--<a href="" class="pure-menu-heading pure-menu-link">Main Menu</a>--}}
    <span>Menu:</span>
    <ul class="pure-menu-list">
        <li class="pure-menu-item"><a href="<?php echo route('estimateHome');?>" class="pure-menu-link">Shipment
                Estimator</a></li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item"><a href="<?php echo route('joinHome');?>" class="pure-menu-link">Adv. Query
                Builder</a></li>
        <li class="pure-menu-item"><span>|</span></li>



        <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
            <a href="#" id="menuLink1" class="pure-menu-link">Packing Lists</a>
            <ul class="pure-menu-children">
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetKohlsPDF');?>" class="pure-menu-link">Kohls.com</a>

                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetQVCPDF');?>" class="pure-menu-link">QVC</a>

                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetBBBPDF');?>" class="pure-menu-link">BBB DS</a>
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetMacysPDF');?>" class="pure-menu-link">Macys</a>
                </li>
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetLAndTPDF');?>" class="pure-menu-link">Lord & Taylor</a>
                </li>

                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetSURPDF');?>" class="pure-menu-link">Surlatable</a>
                </li>
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetNORDPDF');?>" class="pure-menu-link">Nordstrom</a>
                </li>
            </ul>
        </li>






        <li class="pure-menu-item"><span>|</span></li>

        <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
            <a href="#" id="menuLink1" class="pure-menu-link">Descramblers</a>
            <ul class="pure-menu-children">
                <li class="pure-menu-item"><a href="<?php echo route('descrambledo');?>" class="pure-menu-link">Extract DOs</a></li>
                <li class="pure-menu-item"><a href="<?php echo route('descrambleshp');?>" class="pure-menu-link">Extract SHPs</a></li>

            </ul>
        </li>



        <span>|</span>

        <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
            <a href="#" id="menuLink1" class="pure-menu-link">Other Tools</a>
            <ul class="pure-menu-children">
                <li class="pure-menu-item"><a href="<?php echo route('ChargeBackGET');?>" class="pure-menu-link">Parse Chargeback</a></li>

                <li class="pure-menu-item"><a href="<?php echo route('amazoncsvGET');?>" class="pure-menu-link">Amazon CSV Parser</a>
                </li>
            </ul>
        </li>


    </ul>
</div>