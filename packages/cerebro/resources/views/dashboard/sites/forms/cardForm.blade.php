<?php
/**
 * @var $forms array
 * @var $servers array
 * @var $creditCardThemes array
 * @var $site \App\Models\Sites
 * @var $siteItems \App\Models\CardSiteItems
 */

?>

<form id="creditCardForm" class="needs-validation" enctype="multipart/form-data">
    <input type="hidden" name="site_type" value="card">
    <input type="hidden" name="id" value="{{$site->id}}">
    <div class="row">
        <div class="col-4">
            <div class="mb-3">
                <label for="domainName" class="form-label">Domain Name</label>
                <input type="text" maxlength="256" class="form-control"
                       id="domainName" value="{{ $site->domain_name }}"
                       name="domain_name" @if(isset($site->id )) disabled @endif>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <label for="sitesModalForm" class="form-label">Form</label>
                <select class="form-select" id="sitesModalFormId" name="form_id" @if(isset($site->id )) disabled @endif>
                    @php
                        foreach ($forms as $form) { @endphp
                    <option value="{{ $form->id}}"
                            @if($form->id == $site->form_id) selected @endif>{{ $form->name }}</option>
                    @php }
                    @endphp
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <label for="siteServer" class="form-label">Server</label>
                <select class="form-select" id="siteServer" name="server_id" @if(isset($site->id )) disabled @endif>
                    @php
                        foreach ($servers as $server) { @endphp
                    <option value="{{ $server->id}}"
                            @if($server->id == $site->server_id) selected @endif>{{ $server->name }}</option>
                    @php }
                    @endphp
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" maxlength="256" class="form-control" id="title"
                       name="title" value="{{ $site->title }}">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <label for="sitesModalTheme" class="form-label">Theme</label>
                <select class="form-select" id="sitesModalTheme" name="theme">
                    @php
                        foreach ($creditCardThemes as $creditCardTheme) { @endphp
                    <option value="{{ $creditCardTheme['id']}}">{{ $creditCardTheme['name'] }}</option>
                    @php }
                    @endphp
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-7">
            <div class="row" id="logo">
                <div class="col-4">
                    <label for="logo" class="form-label">Logo</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="logo"
                           name="logo">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->logo == null) d-none @endif">
                    <a class="image-preview" href="@if($site->logo != null) {{$site->logo}} @endif"><i class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="favicon">
                <div class="col-4">
                    <label for="sitesModalFavicon" class="form-label">Favicon</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="favicon"
                           name="favicon">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->favicon == null) d-none @endif">
                    <a class="image-preview" href="@if($site->favicon != null) {{$site->favicon}} @endif"><i class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="hero">
                <div class="col-4">
                    <label for="hero" class="form-label">Hero Image</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="hero"
                           name="hero">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->hero == null) d-none @endif">
                    <a class="image-preview" href="@if($site->hero != null) {{$site->hero}} @endif"><i class="fa-solid fa-image"></i></a>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="row" id="header">
                <div class="col-4">
                    <label for="cardHeader" class="form-label">Header</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control"
                           id="cardHeader"
                           name="header" value="{{ $site->header }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="row" id="sub-header">
                <div class="col-4">
                    <label for="cardSubHeader" class="form-label">Sub Header</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control"
                           id="cardSubHeader"
                           name="sub_header" value="{{ $site->sub_header }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="row" id="sub-header-color-text">
                <div class="col-4">
                    <label for="cardSubHeaderColorText" class="form-label">Sub Header Color Text</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control"
                           id="cardSubHeaderColorText"
                           name="sub_header_color_text" value="{{ $site->sub_header_color_text }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="row" id="sub-header-color">
                <div class="col-4">
                    <label for="cardSubHeaderColor" class="form-label">Sub Header Color</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control" data-jscolor="{format : 'hex'}"
                           id="cardSubHeaderColor"
                           name="sub_header_color" value="{{ $site->sub_header_color }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-7">
            <div class="row" id="card-button-text">
                <div class="col-4">
                    <label for="cardButtonText" class="form-label">Button Text</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control"
                           id="cardButtonText"
                           name="card_button_text" value="{{ $site->card_button_text }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="row">
                <div class="col-6">
                    <label for="cardButtonColorFirst" class="form-label">Button First Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="cardButtonColorFirst"
                           name="card_button_color_first" value="{{ $site->card_button_color_first }}">
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="cardButtonColorSecond" class="form-label">Button Second Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="cardButtonColorSecond"
                           name="card_button_color_second" value="{{ $site->card_button_color_second }}">
                </div>
            </div>
        </div>
    </div>
    @php
        $itemNumber = 1;
        foreach ($siteItems as $item) { @endphp

    <div class="row item-row">
        <input type="hidden" class="item-id" name="card_item[{{ $itemNumber }}][id]" value="{{ $item->id }}">
        <div class="col-12">
            <fieldset class="form-group border border-gray border-3 p-3">
                <legend class="legend"><span class="legend-label">Item #<span
                            class="item-number">{{ $itemNumber }}</span></span>
                    <button type="button" class="btn btn-danger float-end delete-item-button"><i
                            class="fa-solid fa-xmark"></i></button>
                </legend>
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Item name</label>
                            </div>
                            <div class="col-7">
                                <input class="form-control item-name"
                                       name="card_item[{{ $itemNumber }}][name]" value="{{ $item->name }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-1">
                                &nbsp;
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Card Image</label>
                            </div>
                            <div class="col-7">
                                <input type="file" class="form-control item-image"
                                       name="card_item[{{ $itemNumber }}][image]">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-1 @if($item->image == null) d-none @endif" id="last_bg-link-wrapper">
                                <a class="image-preview" href="@if($item->image != null) {{Storage::disk('sitesResources')->url($item->image)}} @endif"><i class="fa-solid fa-image"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">Stars</label>
                            </div>
                            <div class="col-7">
                                <input type="number" class="form-control item-stars" min="1" max="5"
                                       name="card_item[{{ $itemNumber }}][stars]" value="{{ $item->stars }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-1">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Button First Color</label>
                            </div>
                            <div class="col-6">
                                <input class="form-control item-button-first-color" data-jscolor="{format : 'hex'}"
                                       name="card_item[{{ $itemNumber }}][btn_color_first]"
                                       value="{{ $item->btn_color_first }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Button Second Color</label>
                            </div>
                            <div class="col-6">
                                <input class="form-control item-button-second-color" data-jscolor="{format : 'hex'}"
                                       name="card_item[{{ $itemNumber }}][btn_color_second]"
                                       value="{{ $item->btn_color_second }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label ">Button Text</label>
                            </div>
                            <div class="col-8">
                                <input class="form-control item-button-text"
                                       name="card_item[{{ $itemNumber }}][btn_text]" value="{{ $item->btn_text }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label ">Offer URL</label>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control item-button-url"
                                       name="card_item[{{ $itemNumber }}][btn_url]" value="{{ $item->btn_url }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                    </div>
                    <div class="col-12">
                        <textarea class="form-control item-description"
                                  name="card_item[{{ $itemNumber }}][description]">{{ $item->description }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <fieldset class="form-group border border-dark border-1 p-3 mt-3">
                    <legend>Benefits</legend>
                    @php
                        $benefitNumber = 0;
                        foreach ($item->benefits as $benefit) {
                    @endphp

                    <div class="row item-benefit">
                        <div class="col-11">
                            <div class="input-group has-validation">
                                                    <span class="input-group-text benefit-label">Benefit #<span
                                                            class="benefit-number">{{ $benefitNumber + 1 }}</span></span>
                                <input class="form-control item-benefit-input"
                                       name="card_item[{{ $itemNumber }}][benefits][{{$benefitNumber}}]"
                                       value="{{ $benefit }}">
                                <span class="invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-1">
                            <button type="button"
                                    class="btn btn-outline-danger float-end delete-item-benefit-button">
                                <i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>

                    @php
                        $benefitNumber++;
                        }
                    @endphp


                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button"
                                    class="btn btn-outline-success float-end add-item-benefit-button">
                                <i
                                    class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </fieldset>
            </fieldset>
        </div>
    </div>
    @php
        $itemNumber++;
                            }
    @endphp
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-success float-end" id="add-item-button">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="float-end mt-5">
        <a href="{{ url('sites') }}">
            <button type="button" class="btn btn-secondary">Back</button>
        </a>
        @if(isset($site->id))
            <button type="button" class="btn btn-primary" id="btnCreditCardUpdate">Update</button>
        @else
            <button type="button" class="btn btn-primary" id="btnCreditCardCreate">Create</button>
        @endif
    </div>
</form>



