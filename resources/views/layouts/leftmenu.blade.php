<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title pt-2" style="display: flex;justify-content: space-between;">
            <a href="/home" class="logo col-11">						
                <img src="/assets/img/white-logo-new.png" class="sidebar-logo" alt="MFI Logo" />
            </a>
            <div class="d-md-none toggle-sidebar-left col-1" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                <i class="fas fa-times" aria-label="Toggle sidebar"></i>
            </div>
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="active">
                        <a class="nav-link" href="/">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            <span>Home</span>
                        </a>    
                    </li>
            
                    @if(((session('user_access')[0]['module_id'])==1 && (session('user_access')[0]['view'])==1) OR ((session('user_access')[1]['module_id'])==2 && (session('user_access')[1]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-users" aria-hidden="true"></i>
                            <span>Users</span>
                        </a>
                        <ul class="nav nav-children">
                            @if((session('user_access')[0]['module_id'])==1 && (session('user_access')[0]['view'])==1)
                            <li>
                                <a class="nav-link"   href="{{ route('all-roles')}}">
                                    Roles
                                </a>
                            </li>	
                            @endif
                            @if((session('user_access')[1]['module_id'])==2 && (session('user_access')[1]['view'])==1)
                            <li>
                                <a class="nav-link"   href="{{ route('all-users')}}">
                                    User Accounts
                                </a>
                            </li>
                            @endif	
                            
                             <!-- set admin Role ID here -->
                             @if(session('user_role')==1)
                            <li>
                                <a class="nav-link"   href="{{ route('all-modules')}}">
                                    Modules
                                </a>
                            </li>	
                            @endif
                        </ul>
                    </li>
                    @endif


                    @if(((session('user_access')[2]['module_id'])==3 && (session('user_access')[2]['view'])==1) OR ((session('user_access')[3]['module_id'])==4 && (session('user_access')[3]['view'])==1) OR ((session('user_access')[4]['module_id'])==5 && (session('user_access')[4]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-warehouse" aria-hidden="true"></i>
                            <span>Items</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[2]['module_id'])==3 && (session('user_access')[2]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-item-groups')}}">
                                    <span>Item Groups</span>
                                </a>
                            </li>	
                            @endif
                            @if((session('user_access')[3]['module_id'])==4 && (session('user_access')[3]['view'])==1)
                            <li>
                                <a class="nav-link"   href="{{ route('all-items')}}">
                                    Items
                                </a>
                            </li>
                            @endif
                            @if((session('user_access')[4]['module_id'])==5 && (session('user_access')[4]['view'])==1)

                            <li>
                                <a class="nav-link"   href="{{ route('all-items-2')}}">
                                    Item Pipes
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(((session('user_access')[5]['module_id'])==6 && (session('user_access')[5]['view'])==1) OR ((session('user_access')[6]['module_id'])==7 && (session('user_access')[6]['view'])==1) OR ((session('user_access')[7]['module_id'])==8 && (session('user_access')[7]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px"class="fa fa-money-bill" aria-hidden="true"></i>
                            <span>Accounts</span>                
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[5]['module_id'])==6 && (session('user_access')[5]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{route('all-acc')}}">
                                    Chart Of Accounts
                                </a>
                            </li>
                            @endif
                            @if(((session('user_access')[6]['module_id'])==7 && (session('user_access')[6]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-acc-groups')}}">
                                    COA Groups
                                </a>
                            </li>	
                            @endif

                            @if((session('user_access')[7]['module_id'])==8 && (session('user_access')[7]['view'])==1)
                            <li>
                                <a class="nav-link"   href="{{ route('all-acc-sub-heads-groups')}}">
                                    COA Sub Heads
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif


                    @if(((session('user_access')[8]['module_id'])==9 && (session('user_access')[8]['view'])==1) OR ((session('user_access')[9]['module_id'])==10 && (session('user_access')[9]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-file-invoice" aria-hidden="true"></i>
                            <span>Vouchers</span>     
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[8]['module_id'])==9 && (session('user_access')[8]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-jv1')}}">
                                    Journal Voucher 1
                                </a>
                            </li>	
                            @endif

                            @if(((session('user_access')[9]['module_id'])==10 && (session('user_access')[9]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-jv2')}}">
                                    Journal Voucher 2
                                </a>
                            </li>	
                            @endif
                        </ul>
                    </li>
                    @endif


                    @if(((session('user_access')[10]['module_id'])==11 && (session('user_access')[10]['view'])==1) OR ((session('user_access')[11]['module_id'])==12 && (session('user_access')[11]['view'])==1) OR ((session('user_access')[12]['module_id'])==13 && (session('user_access')[12]['view'])==1) OR ((session('user_access')[13]['module_id'])==14 && (session('user_access')[13]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-file-import" aria-hidden="true"></i>
                            <span>Purchase</span>     
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[10]['module_id'])==11 && (session('user_access')[10]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-purchases1')}}">
                                    Purchase 1
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[11]['module_id'])==12 && (session('user_access')[11]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-purchases2')}}" >
                                    Purchase 2
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[12]['module_id'])==13 && (session('user_access')[12]['view'])==1))
                            <li>
                                <a class="nav-link" href="#">
                                    Purchase 1 Return
                                </a>
                            </li>	
                            @endif

                            @if(((session('user_access')[13]['module_id'])==14 && (session('user_access')[13]['view'])==1))
                            <li>
                                <a class="nav-link" href="#">
                                    Purchase 2 Return
                                </a>
                            </li>			
                            @endif						
                        </ul>
                    </li>
                    @endif						


                    @if(((session('user_access')[14]['module_id'])==15 && (session('user_access')[14]['view'])==1) OR ((session('user_access')[15]['module_id'])==16 && (session('user_access')[15]['view'])==1) OR ((session('user_access')[16]['module_id'])==17 && (session('user_access')[16]['view'])==1) OR ((session('user_access')[17]['module_id'])==18 && (session('user_access')[17]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-file-export" aria-hidden="true"></i>
                            <span>Sale</span>   
                        </a>
                        <ul class="nav nav-children">

                            @if(((session('user_access')[14]['module_id'])==15 && (session('user_access')[14]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-saleinvoices')}}">
                                    Sale 1
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[15]['module_id'])==16 && (session('user_access')[15]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-sale2invoices')}}">
                                    Sale 2
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[16]['module_id'])==17 && (session('user_access')[16]['view'])==1))
                            <li>
                                <a class="nav-link" href="#">
                                    Sale 1 Return
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[17]['module_id'])==18 && (session('user_access')[17]['view'])==1))
                            <li>
                                <a class="nav-link" href="#">
                                    Sale 2 Return
                                </a>
                            </li>			
                            @endif
                            
                            
                        </ul>
                    </li>
                    @endif

                    @if(((session('user_access')[18]['module_id'])==19 && (session('user_access')[18]['view'])==1) OR ((session('user_access')[19]['module_id'])==20 && (session('user_access')[19]['view'])==1) OR ((session('user_access')[20]['module_id'])==21 && (session('user_access')[20]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link  " href="#">
                            <i style="font-size:16px" class="fa fa-cube" aria-hidden="true"></i>
                            <span>Stock Pipe</span>   
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[18]['module_id'])==19 && (session('user_access')[18]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-tstock-in')}}">
                                    Stock In
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[19]['module_id'])==20 && (session('user_access')[19]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-tstock-out')}}">
                                    Stock Out
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[20]['module_id'])==21 && (session('user_access')[20]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-tbad-dabs')}}">
                                    Stock Balance
                                </a>
                            </li>	
                            @endif							
                        </ul>
                    </li>
                    @endif


                    @if(((session('user_access')[21]['module_id'])==22 && (session('user_access')[21]['view'])==1) OR ((session('user_access')[22]['module_id'])==23 && (session('user_access')[22]['view'])==1) OR ((session('user_access')[23]['module_id'])==24 && (session('user_access')[23]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-door-open" aria-hidden="true"></i>
                            <span>Stock Doors</span>   
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[21]['module_id'])==22 && (session('user_access')[21]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-stock-in')}}">
                                    Stock In
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[22]['module_id'])==23 && (session('user_access')[22]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-stock-out')}}">
                                    Stock Out
                                </a>
                            </li>
                            @endif

                            @if(((session('user_access')[23]['module_id'])==24 && (session('user_access')[23]['view'])==1))
                            <li>
                                <a class="nav-link" href="{{ route('all-bad-dabs')}}">
                                    Stock Balance
                                </a>
                            </li>
                            @endif								
                        </ul>
                    </li>
                    @endif

                    @if(((session('user_access')[24]['module_id'])==25 && (session('user_access')[24]['view'])==1) OR ((session('user_access')[25]['module_id'])==26 && (session('user_access')[25]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link  " href="#">
                            <i style="font-size:16px" class='fa fa-clipboard'></i>
                            <span>Purchase Orders</span>  
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[24]['module_id'])==25 && (session('user_access')[24]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-po')}}">
                                    P.O 
                                </a>
                            </li>	
                            @endif

                            @if(((session('user_access')[25]['module_id'])==26 && (session('user_access')[25]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-tpo')}}">
                                    P.O Pipe/Garder
                                </a>
                            </li>		
                            @endif		

                        </ul>
                    </li>
                    @endif

                    @if(((session('user_access')[26]['module_id'])==27 && (session('user_access')[26]['view'])==1) OR ((session('user_access')[27]['module_id'])==28 && (session('user_access')[27]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px" class="fa fa-file" aria-hidden="true"></i>
                            <span>Quotations</span>  
                        </a>
                        <ul class="nav nav-children">
                            @if(((session('user_access')[26]['module_id'])==27 && (session('user_access')[26]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-quotation')}}">
                                    Quotation 1
                                </a>
                            </li>	
                            @endif

                            @if(((session('user_access')[27]['module_id'])==28 && (session('user_access')[27]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-tquotation')}}">
                                    Quotation 2
                                </a>
                            </li>
                            @endif							
                        </ul>
                    </li>
                    @endif

                    @if(((session('user_access')[28]['module_id'])==29 && (session('user_access')[28]['view'])==1) OR ((session('user_access')[29]['module_id'])==30 && (session('user_access')[29]['view'])==1))
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i style="font-size:16px"class="fa fa-arrow-right"></i>
                            <span>Others</span>  
                        </a>
                        <ul class="nav nav-children">

                            @if(((session('user_access')[28]['module_id'])==29 && (session('user_access')[28]['view'])==1))
                            <li>
                                
                                <a class="nav-link"   href="{{ route('all-complains')}}">
                                    Complains
                                </a>
                            </li>	
                            @endif

                            @if(((session('user_access')[29]['module_id'])==30 && (session('user_access')[29]['view'])==1))
                            <li>
                                <a class="nav-link"   href="{{ route('all-weight')}}">
                                    Weight
                                </a>
                            </li>	
                            @endif
                        </ul>
                    </li>
                    @endif

                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="fa fa-file-pdf" style="font-size:16px;"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="nav nav-children">
                            @if((session('user_access')[30]['module_id'])==31 && (session('user_access')[30]['view']) == 1)
                                <li>
                                    <a class="nav-link" href="{{ route('rep-by-acc-name')}}">
                                        Account Name
                                    </a>
                                </li>
                            @endif
                            @if((session('user_access')[31]['module_id'])==32 && (session('user_access')[31]['view']) == 1)
                                <li>
                                    <a class="nav-link" href="{{ route('rep-by-acc-grp')}}">
                                        Account Group
                                    </a>
                                </li>
                            @endif    
                            @if((session('user_access')[34]['module_id'])==35 && (session('user_access')[34]['view']) == 1)
                                <li>
                                    <a class="nav-link" href="{{ route('rep-daily-register')}}">
                                        Daily Register
                                    </a>
                                </li>
                            @endif
                            <li class="nav-parent">
                                <a class="nav-link" href="#">
                                    <span>Item Name</span>
                                </a>
                                <ul class="nav nav-children">
                                    <li>
                                        <a class="nav-link" href="{{ route('rep-by-item-name1')}}">
                                            By Item Name 1
                                        </a>
                                    </li>
                                    <li>
                                        <a class="nav-link" href="#">
                                            By Item Name 2
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a class="nav-link" href="#">
                                    Item Group
                                </a>
                            </li>
                            @if((session('user_access')[35]['module_id'])==36 && (session('user_access')[35]['view']) == 1)
                                <li>
                                    <a class="nav-link" href="{{ route('rep-commissions')}}">
                                        Commissions
                                    </a>
                                </li>
                            @endif
                            @if((session('user_access')[32]['module_id'])==33 && (session('user_access')[32]['view']) == 1 || (session('user_access')[33]['module_id'])==34 && (session('user_access')[33]['view']) == 1 )
                                <li class="nav-parent">
                                    <a class="nav-link" href="#">
                                        <i class="fa fa-warehouse" style="font-size:16px;"></i>
                                        <span>GoDown</span>
                                    </a>
                                    <ul class="nav nav-children">
                                        @if((session('user_access')[32]['module_id'])==33 && (session('user_access')[32]['view']) == 1)
                                        <li>
                                            <a class="nav-link" href="/rep-godown-by-item-name">
                                                By Item Name
                                            </a>
                                        </li>
                                        @endif
                                        @if((session('user_access')[33]['module_id'])==34 && (session('user_access')[33]['view']) == 1)
                                        <li>
                                            <a class="nav-link" href="/rep-godown-by-group-name">
                                                By Item Group
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                            <li class="nav-parent">
                                <a class="nav-link" href="#">
                                    <i class="fa fa-industry" style="font-size:16px;"></i>
                                    <span>Factory</span>
                                </a>
                                <ul class="nav nav-children">
                                    <li>
                                        <a class="nav-link" href="#">
                                            By Item Name
                                        </a>
                                    </li>
                                    <li>
                                        <a class="nav-link" href="#">
                                            By Item Group
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                </ul>	
            </nav>
		</div>

        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');

                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>