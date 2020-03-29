
@extends('layout.main')

@section('title') ALERT @endsection
@section('m_alert') class = 'active' @endsection

@section('body')
<!-- Page header -->
<div class="page-header page-header-default ">
    <!-- <div class="page-header-content">
            <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Home</span> - Dashboard</h4>
            </div>

            <div class="heading-elements">
                    <div class="heading-btn-group">
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
                            <a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
                    </div>
            </div>
    </div> -->

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">@yield('title')</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-gear position-left"></i>
                    Settings
                    <span class="caret"></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
                    <li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
                    <li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
                    <li class="divider"></li>
                    <li><a href="#"><i class="icon-gear"></i> All settings</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- /page header -->


<!-- Content area -->
<div class="content">


    <div class="col-md-12">

        <!-- Basic layout-->
        <form action="#">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">ALERT</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="row">




                        <div class="form-group col-md-8">

                            <legend class="text-bold">Sweet Alert</legend>

                            <button type="button" class="btn btn-default btn-sm" data-popup="tooltip-custom" title="I'm a teal tooltip">Tooltip <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_basic">Basic alert <i class="icon-play3 position-right"></i></button>


                            <button type="button" class="btn btn-danger btn-sm" id="sweet_title_text">Title with a text <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_auto_closer">Auto closer <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_prompt">Prompt dialog <i class="icon-play3 position-right"></i></button>

                        </div>


                        <div class="form-group col-md-8">

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_loader">With a loader <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_html">With HTML message <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_success">Success message <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_error">Error message <i class="icon-play3 position-right"></i></button>



                        </div>

                        <div class="form-group col-md-8">

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_warning">Warning message <i class="icon-play3 position-right"></i></button>


                            <button type="button" class="btn btn-danger btn-sm" id="sweet_info">Info message <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-danger btn-sm" id="sweet_combine">Combine messages <i class="icon-play3 position-right"></i></button>



                        </div>


                        <div class="form-group col-md-12">

                            <legend class="text-bold">Modal</legend>

                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal_mini">Mini size modal <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal_small">Small size modal <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal_default">Default size modal <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal_large">Large size modal <i class="icon-play3 position-right"></i></button>

                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal_full">Full size modal <i class="icon-play3 position-right"></i></button>



                        </div>

                        <legend class="text-bold col-md-12">Alert</legend>
                        <div class="col-md-6">
                            <p class="text-semibold">Primary alert</p>
                            <div class="alert alert-primary no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Morning!</span> We're glad to <a href="#" class="alert-link">see you again</a> and wish you a nice day.
                            </div>

                            <p class="text-semibold">Danger alert</p>
                            <div class="alert alert-danger no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Oh snap!</span> Change a few things up and <a href="#" class="alert-link">try submitting again</a>.
                            </div>

                            <p class="text-semibold">Success alert</p>
                            <div class="alert alert-success no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Well done!</span> You successfully read <a href="#" class="alert-link">this important</a> alert message.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <p class="text-semibold">Warning alert</p>
                            <div class="alert alert-warning no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Warning!</span> Better <a href="#" class="alert-link">check yourself</a>, you're not looking too good.
                            </div>

                            <p class="text-semibold">Info alert</p>
                            <div class="alert alert-info no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Heads up!</span> This alert needs your attention, but it's not <a href="#" class="alert-link">super important</a>.
                            </div>

                            <p class="text-semibold">Custom color</p>
                            <div class="alert text-violet-800 alpha-violet no-border">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">Surprise!</span> This is a super-duper nice looking <a href="#" class="alert-link">alert</a> with custom color.
                            </div>
                        </div>

















                    </div>	



                </div>
            </div>
        </form>
        <!-- /basic layout -->

    </div>






</div>
<!-- /latest posts -->


<!-- Basic modal -->
<div id="modal_default" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Basic modal</h5>
            </div>

            <div class="modal-body">
                <h6 class="text-semibold">Text in a modal</h6>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

                <hr>

                <h6 class="text-semibold">Another paragraph</h6>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /basic modal -->



<!-- Mini modal -->
<div id="modal_mini" class="modal fade">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Mini modal</h5>
            </div>

            <div class="modal-body">
                <h6 class="text-semibold">Text in a modal</h6>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

                <hr>

                <h6 class="text-semibold">Another paragraph</h6>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /mini modal -->


<!-- Small modal -->
<div id="modal_small" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Small modal</h5>
            </div>

            <div class="modal-body">
                <h6 class="text-semibold">Text in a modal</h6>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

                <hr>

                <h6 class="text-semibold">Another paragraph</h6>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /small modal -->


<!-- Large modal -->
<div id="modal_large" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Large modal</h5>
            </div>

            <div class="modal-body">
                <h6 class="text-semibold">Text in a modal</h6>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

                <hr>

                <h6 class="text-semibold">Another paragraph</h6>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /large modal -->


<!-- Full width modal -->
<div id="modal_full" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Full width modal</h5>
            </div>

            <div class="modal-body">
                <h6 class="text-semibold">Text in a modal</h6>
                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>

                <hr>

                <h6 class="text-semibold">Another paragraph</h6>
                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- /full width modal -->


@endsection




@section('javascripy') 

<script type="text/javascript" src="assets/js/plugins/notifications/bootbox.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>

<script type="text/javascript" src="assets/js/pages/components_modals.js"></script>





@endsection
