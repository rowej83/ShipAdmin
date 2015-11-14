<div class="pure-menu pure-menu-horizontal" style="margin-top:20px;">
    {{--<a href="" class="pure-menu-heading pure-menu-link">Main Menu</a>--}}
    <span>Menu:</span>
    <ul class="pure-menu-list">
        <li class="pure-menu-item"><a href="<?php echo route('estimateHome');?>" class="pure-menu-link">Shipment
                Estimator</a></li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item"><a href="<?php echo route('joinHome');?>" class="pure-menu-link">Advanced Query
                Builder</a></li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item"><a href="<?php echo route('parseGetPDF');?>" class="pure-menu-link">Kohls.com</a>
        </li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item"><a href="<?php echo route('parseGetKohlsPDF');?>" class="pure-menu-link">Kohls DB</a>
        </li>
        <li class="pure-menu-item"><span>|</span></li>
        <li class="pure-menu-item"><a href="<?php echo route('retrieveGetKohlsPDF');?>" class="pure-menu-link">Kohls
                Retrieve PLs</a></li>


    </ul>
</div>