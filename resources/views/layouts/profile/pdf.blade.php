<!-- main body start --><body>
    <table border="0" cellspacing="0" cellpadding="0" class="wrapper">
        <tr>
            <td>
            <!-- main section start -->
                <table border="0" cellspacing="0" cellpadding="0" class="main-section">
                	<tr>
                        <td width="25%" valign="top" rowspan="7">
                        	 @if($pedigree['g1']->user->pedigree_logo['path'])
                                <img src="{{ $pedigree['g1']->user->pedigree_logo['path']}}" class="logo">
                            @endif
                            <br>
                            <small class="from_who">
                            {!! nl2br($pedigree['g1']->user->pedigree_rabbitry_information) !!}
                            </small>
                            <br><br>
                            @include('layouts.profile.pdf_main', ['breeder' => $pedigree['g1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            <!-- QR code -->
                            @if(!isset($isPublic))
                            <br>
                            <table border="0" cellspacing="0" cellpadding="0" class="table">
                              <tr>
                                  <td width="1">
                                      @if(@$pedigree['g1']->custom_id)
                                          <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(100)->generate( route('web.kit.pedigree', ['id' => $pedigree['g1']->token])  )) }} ">
                                      @else
                                          <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(100)->generate( route('web.pedigree', ['id' => $pedigree['g1']->token])  )) }} ">
                                      @endif
                                  </td>
                                  <td>Verify this pedigree online by scanning the QR code on the left or by visiting
                                      @if(@$pedigree['g1']->custom_id)
                                          <a href="{{ route('web.kit.pedigree', ['id' => $pedigree['g1']->token])}}">{{route('web.kit.pedigree', ['id' => $pedigree['g1']->token])}}</a>
                                      @else
                                          <a href="{{ route('web.pedigree', ['id' => $pedigree['g1']->token])}}">{{route('web.pedigree', ['id' => $pedigree['g1']->token])}}</a>
                                      @endif
                                  </td>
                              </tr>
                            </table>
                            @endif
                            <p>I hereby certify that this pedigree is correct to the best of my knowledge and belief.</p>
                        </td>
                        <td width="25%" class="padding_space" valign="middle" rowspan="4">
                            @if(isset($pedigree['g2']['f1']))
                            	@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g2']['f1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                    		@endif
                        </td>
                        <td width="25%" class="padding_space" valign="middle" rowspan="2">
                        	 @if(isset($pedigree['g3']['f1']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g3']['f1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                             @endif
                        </td>
                        <td width="25%" class="padding_space">
                            @if(isset($pedigree['g4']['f1']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['f1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>
                    <!-- 2nd row -->
                    <tr>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['m1']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['m1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>

                    <!-- 3rd row -->
                    <tr>
                        <td class="padding_space" valign="middle" rowspan="2">
                        	@if(isset($pedigree['g3']['m1']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g3']['m1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                             @endif
                        </td>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['f2']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['f2'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>

                    <!-- 4th row -->
                    <tr>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['m2']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['m2'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>

                    <!-- 5th row -->
                    <tr>
                        <td class="padding_space" valign="middle" rowspan="4">
                        	@if(isset($pedigree['g2']['m1']))
                            	@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g2']['m1'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                    		@endif
                        </td>
                        <td class="padding_space" valign="middle" rowspan="2">
                        	@if(isset($pedigree['g3']['f2']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g3']['f2'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                             @endif
                        </td>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['f3']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['f3'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>

                    <!-- 6th row -->
                    <tr>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['m3']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['m3'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                         </td>
                    </tr>

                    <!-- 7th row -->
                    <tr>
                        <td class="padding_space" valign="middle" rowspan="2">
                        	@if(isset($pedigree['g3']['m2']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g3']['m2'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                             @endif
                        </td>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['f4']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['f4'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>

                    <!-- 8th row -->
                    <tr>
                    	<td valign="bottom">Try Hutch for FREE: <a href="{{ route('web.invite', [ 'inviter' => $owner->getSlug()], true) }}">{{ route('web.invite', [ 'inviter' => $owner->getSlug()], true) }}</a></td>
                        <td class="padding_space">
                        	@if(isset($pedigree['g4']['m4']))
                    			@include('layouts.profile.pdf_other', ['breeder' => $pedigree['g4']['m4'], 'directory'=>$directory,'isPublic'=>isset($isPublic) ? $isPublic : false])
                            @endif
                        </td>
                    </tr>
                </table>
                <!-- main section end -->
            </td>
        </tr>
    </table>
    
</body>

 <!-- main body end -->

 
<style>
    @media screen {
        body {
            width: 29.7cm;
            margin: 0;
        }
    }
 @if (\Request::has('origsize'))
     * {
         font-size: inherit;
         font-family: inherit;
     }
 @endif

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-weight: 400;
            font-size: 10px;
            color: #000;
        }
		.wrapper {
            margin: 0;
        }

        table {
            width: 100%;
        }

        img {
         /*       max-width: 100%; */
        }
        .img-circle {
        /*    border: 1px solid #000; */
        }
        .bg-white {
        /*    border: 1px solid #000; */
            color: #000;
            padding: 5px;
			background-color:#ececec;
        }
		.main-grey {
			/*background-color: #ececec;  */
        }
		.name {
			font-size:12px;
			padding-bottom: 2px;
		}

        .no-boder-left {
            border-left: 0;
        }
        .logo {
            padding-bottom: 5px;
			max-height:300px;
            max-width: 250px;

        }
        .table-bordered,
        .table-bordered td {
        /*    border: 1px solid #000; */
            border-collapse: collapse;
        }

        .from_who {
            font-size: 12px;
            line-height: 1.2;
        }
		.main-top {
			line-height: 1;
			font-size: 20px;
			font-weight: 700;
			padding-left:5px;
		}
		.main-top small {
			font-weight: 400;
		}

        .padding_space {
            padding: 2px 5px;
        }
		.cell-padding {
			padding: 5px 10px;
		}
		.border {
			border: 1px solid #000;
		}


        .footer {
        /*    border-top: 1px solid #ddd; */
        }
</style>
