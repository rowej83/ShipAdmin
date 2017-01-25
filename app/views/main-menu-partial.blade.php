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
        <li class="pure-menu-item"><a href="<?php echo route('descrambledo');?>" class="pure-menu-link">Extract DOs</a></li>
        <li class="pure-menu-item"><span>|</span></li>



        <li class="pure-menu-item"><a href="<?php echo route('retrieveGetKohlsPDF');?>" class="pure-menu-link">Kohls.com</a>
        </li><span>|</span>


        <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
            <a href="#" id="menuLink1" class="pure-menu-link">Other Tools</a>
            <ul class="pure-menu-children">
                <li class="pure-menu-item"><a href="<?php echo route('ChargeBackGET');?>" class="pure-menu-link">Parse Chargeback</a></li>
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetMacysPDF');?>" class="pure-menu-link">Macys Packing Lists</a>
                </li>
                <li class="pure-menu-item"><a href="<?php echo route('retrieveGetLAndTPDF');?>" class="pure-menu-link">Lord & Taylor Packing Lists</a>
                </li>
                <li class="pure-menu-item"><a href="<?php echo route('amazoncsvGET');?>" class="pure-menu-link">Amazon CSV Parser</a>
                </li>
            </ul>
        </li>


    </ul>
</div>