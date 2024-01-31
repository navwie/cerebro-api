<?php
/**
 * @var $forms array
 * @var $servers array
 * @var $loanFormThemes array
 * @var $site \App\Models\Sites
 */
?>
<form id="sitesForm" enctype="multipart/form-data">
    <input type="hidden" name="site_type" value="loan">
    <input type="hidden" name="id" value="{{$site->id}}">
    <div class="row">
        <div class="col-4">
            <div class="mb-3">
                <label for="sitesModalDomainName" class="form-label">Domain Name</label>
                <input type="text" maxlength="256" class="form-control"
                       id="sitesModalDomainName"
                       name="domain_name" value="{{ $site->domain_name }}" @if(isset($site->id )) disabled @endif>
                <div id="sitesModalDomainNameFeedback" class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <label for="sitesModalFormId" class="form-label">Form</label>
                <select class="form-select" id="sitesModalFormId" name="form_id" @if(isset($site->id )) disabled @endif>
                    @php
                        foreach ($forms as $form) { @endphp
                    <option value="{{ $form['id']}}"
                            @if($form->id == $site->form_id) selected @endif>{{ $form['name'] }}</option>
                    @php }
                    @endphp
                </select>
                <div id="sitesModalFormIdFeedback" class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <label for="sitesModalServer" class="form-label">Server</label>
                <select class="form-select" id="sitesModalServer" name="server_id"
                        @if(isset($site->id )) disabled @endif>
                    @php
                        foreach ($servers as $server) { @endphp
                    <option value="{{ $server->id}}"
                            @if($server->id == $site->server_id) selected @endif>{{ $server->name }}</option>
                    @php }
                    @endphp
                </select>
                <div id="sitesModalServerFeedback" class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="mb-3">
                <label for="sitesModalTitle" class="form-label">Title</label>
                <input type="text" maxlength="256" class="form-control" id="sitesModalTitle"
                       name="title" value="{{ $site->title }}">
                <div id="sitesModalTitleFeedback" class="invalid-feedback"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <label for="sitesModalTheme" class="form-label">Theme</label>
                <select class="form-select" id="sitesModalTheme" name="theme">
                    @php
                        foreach ($loanFormThemes as $loanFormTheme) { @endphp
                    <option value="{{ $loanFormTheme['id'] }}" {{ isset($loanFormTheme['disabled']) ? 'disabled' : '' }}
                            @if($loanFormTheme['id'] == $site->theme) selected @endif>{{ $loanFormTheme['name'] }}</option>
                    @php }
                    @endphp
                </select>
                <div id="sitesModalThemeFeedback" class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-7">
            <div class="row" id="logo">
                <div class="col-4">
                    <label for="sitesModalLogo" class="form-label">Logo</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalLogo"
                           name="logo">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->logo == null) d-none @endif" id="logo-link-wrapper">
                    <a class="image-preview" href="@if($site->logo != null) {{$site->logo}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="footer_logo">
                <div class="col-4">
                    <label for="sitesModalLogoFooter" class="form-label">LogoFooter</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalLogoFooter"
                           name="footer_logo">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->footer_logo == null) d-none @endif" id="footer_logo-link-wrapper">
                    <a class="image-preview" href="@if($site->footer_logo != null) {{$site->footer_logo}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="favicon">
                <div class="col-4">
                    <label for="sitesModalFavicon" class="form-label">Favicon</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalFavicon"
                           name="favicon">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->favicon == null) d-none @endif" id="favicon-link-wrapper">
                    <a class="image-preview" href="@if($site->favicon != null) {{$site->favicon}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="hero">
                <div class="col-4">
                    <label for="sitesModalHero" class="form-label">Hero Image</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalHero"
                           name="hero">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->hero == null) d-none @endif" id="hero-link-wrapper">
                    <a class="image-preview" href="@if($site->hero != null) {{$site->hero}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="hero2">
                <div class="col-4">
                    <label for="sitesModalHero" class="form-label">Hero Image 2</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalHero2"
                           name="hero2">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->hero2 == null) d-none @endif" id="hero2-link-wrapper">
                    <a class="image-preview" href="@if($site->hero != null) {{$site->hero}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="why_us_bg">
                <div class="col-4">
                    <label for="sitesModalWhyUsBg" class="form-label">WhyUsBG</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalWhyUsBg"
                           name="why_us_bg">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->why_us_bg == null) d-none @endif" id="why_us_bg-link-wrapper">
                    <a class="image-preview" href="@if($site->why_us_bg != null) {{$site->why_us_bg}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="why_us_bg2">
                <div class="col-4">
                    <label for="sitesModalWhyUsBg2" class="form-label">WhyUsBG2</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalWhyUsBg2"
                           name="why_us_bg2">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->why_us_bg2 == null) d-none @endif" id="why_us_bg2-link-wrapper">
                    <a class="image-preview" href="@if($site->why_us_bg2 != null) {{$site->why_us_bg2}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="why_us_icon_fast">
                <div class="col-4">
                    <label for="sitesModalWhyUsIconFast" class="form-label">WhyUs Icon Fast</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalWhyUsIconFast"
                           name="why_us_icon_fast">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->why_us_icon_fast == null) d-none @endif" id="why_us_icon_fast-link-wrapper">
                    <a class="image-preview" href="@if($site->why_us_icon_fast != null) {{$site->why_us_icon_fast}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="why_us_icon_easy">
                <div class="col-4">
                    <label for="sitesModalWhyUsIconEasy" class="form-label">WhyUs Icon Easy</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalWhyUsIconEasy"
                           name="why_us_icon_easy">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->why_us_icon_easy == null) d-none @endif" id="why_us_icon_easy-link-wrapper">
                    <a class="image-preview" href="@if($site->why_us_icon_easy != null) {{$site->why_us_icon_easy}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="why_us_icon_secure">
                <div class="col-4">
                    <label for="sitesModalWhyUsIconSecure" class="form-label">WhyUs Icon Secure</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalWhyUsIconSecure"
                           name="why_us_icon_secure">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->why_us_icon_secure == null) d-none @endif" id="why_us_icon_secure-link-wrapper">
                    <a class="image-preview" href="@if($site->why_us_icon_secure != null) {{$site->why_us_icon_secure}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="advantages_bg">
                <div class="col-4">
                    <label for="sitesModalAdvantagesBg"
                           class="form-label">AdvantagesBG</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalAdvantagesBg"
                           name="advantages_bg">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->advantages_bg == null) d-none @endif" id="advantages_bg-link-wrapper">
                    <a class="image-preview" href="@if($site->advantages_bg != null) {{$site->advantages_bg}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="comfort_bg">
                <div class="col-4">
                    <label for="sitesModalComfortBg" class="form-label">Comfort(How it works)BG</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalComfortBg"
                           name="comfort_bg">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->comfort_bg == null) d-none @endif" id="comfort_bg-link-wrapper">
                    <a class="image-preview" href="@if($site->comfort_bg != null) {{$site->comfort_bg}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="comfort_bg2">
                <div class="col-4">
                    <label for="sitesModalComfortBg2" class="form-label">Comfort(How it works)BG2</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalComfortBg2"
                           name="comfort_bg2">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->comfort_bg2 == null) d-none @endif" id="comfort_bg2-link-wrapper">
                    <a class="image-preview" href="@if($site->comfort_bg2 != null) {{$site->comfort_bg2}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="comfort_bg3">
                <div class="col-4">
                    <label for="sitesModalComfortBg3" class="form-label">Comfort(How it works)BG3</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalComfortBg3"
                           name="comfort_bg3">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->comfort_bg3 == null) d-none @endif" id="comfort_bg3-link-wrapper">
                    <a class="image-preview" href="@if($site->comfort_bg3 != null) {{$site->comfort_bg3}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="comfort_bg4">
                <div class="col-4">
                    <label for="sitesModalComfortBg4" class="form-label">Comfort(How it works)BG4</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalComfortBg4"
                           name="comfort_bg4">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->comfort_bg4 == null) d-none @endif" id="comfort_bg4-link-wrapper">
                    <a class="image-preview" href="@if($site->comfort_bg4 != null) {{$site->comfort_bg4}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
            <div class="row" id="last_bg">
                <div class="col-4">
                    <label for="sitesModalLastBg" class="form-label">LastBg</label>
                </div>
                <div class="col-6">
                    <input type="file" class="form-control"
                           id="sitesModalLastBg"
                           name="last_bg">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-2 @if($site->last_bg == null) d-none @endif" id="last_bg-link-wrapper">
                    <a class="image-preview" href="@if($site->last_bg != null) {{$site->last_bg}} @endif"><i
                            class="fa-solid fa-image"></i></a>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="row">
                <div class="col-6">
                    <label for="sitesModalMainColor" class="form-label">Main Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalMainColor"
                           name="main_color" value="{{ $site->main_color }}">
                </div>
            </div>
            <div class="row" id="button_color">
                <div class="col-6">
                    <label for="sitesModalButtonColor" class="form-label">Button
                        Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalButtonColor"
                           name="button_color" value="{{ $site->button_color }}">
                </div>
            </div>
            <div class="row" id="button_color_second">
                <div class="col-6">
                    <label for="sitesModalLinkColor" class="form-label">Button Color Second</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalBtnColorSecond"
                           name="button_color_second" value="{{ $site->button_color_second }}">
                </div>
            </div>
            <div class="row" id="step_bar_color">
                <div class="col-6">
                    <label for="sitesModalLinkColor" class="form-label">Step Bar Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalStepBarColor"
                           name="step_bar_color" value="{{ $site->step_bar_color }}">
                </div>
            </div>
            <div class="row" id="link_color">
                <div class="col-6">
                    <label for="sitesModalLinkColor" class="form-label">Link Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalLinkColor"
                           name="link_color" value="{{ $site->link_color }}">
                </div>
            </div>
            <div class="row" id="radio_button">
                <div class="col-6">
                    <label for="sitesModalRadioColor" class="form-label">Radio Button
                        Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalRadioColor"
                           name="radio_color" value="{{ $site->radio_color }}">
                </div>
            </div>
            <div class="row" id="radio_text_button">
                <div class="col-6">
                    <label for="sitesModalRadioTextColor" class="form-label">Radio Button
                        Text
                        Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalRadioTextColor"
                           name="radio_text_color" value="{{ $site->radio_text_color }}">
                </div>
            </div>
            <div class="row" id="header_color">
                <div class="col-6">
                    <label for="sitesModalHeaderColor" class="form-label">Header
                        Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalHeaderColor"
                           name="header_color" value="{{ $site->header_color }}">
                </div>
            </div>
            <div class="row" id="header_bg_color">
                <div class="col-6">
                    <label for="sitesModalHeaderBackgroundColor" class="form-label">Header Background
                        Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalHeaderBackgroundColor"
                           name="header_bg_color" value="{{ $site->header_bg_color }}">
                </div>
            </div>
            <div class="row" id="why_us_bg_color">
                <div class="col-6">
                    <label for="sitesModalWhyUsBackgroundColor" class="form-label">WHyUs Background Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalWhyUsBackgroundColor"
                           name="why_us_bg_color" value="{{ $site->why_us_bg_color }}">
                </div>
            </div>
            <div class="row" id="why_us_text_color">
                <div class="col-6">
                    <label for="sitesModalWhyUsTextColor" class="form-label">WHyUs Text Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalWhyUsTextColor"
                           name="why_us_text_color" value="{{ $site->why_us_text_color }}">
                </div>
            </div>
            <div class="row" id="why_us_cards_text_color">
                <div class="col-6">
                    <label for="sitesModalWhyUsCardsTextColor" class="form-label">WHyUs cards Text Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalWhyUsCardsTextColor"
                           name="why_us_cards_text_color" value="{{ $site->why_us_cards_text_color }}">
                </div>
            </div>
            <div class="row" id="why_us_cards_bg_color">
                <div class="col-6">
                    <label for="sitesModalWhyUsCardsBackgroundColor" class="form-label">WHyUs Cards Background Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hexa'}"
                           id="sitesModalWhyUsCardsBackgroundColor"
                           name="why_us_cards_bg_color" value="{{ $site->why_us_cards_bg_color }}">
                </div>
            </div>
            <div class="row" id="footer_background_color">
                <div class="col-6">
                    <label for="sitesModalFooterBackgroungColor" class="form-label">Footer Background Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalFooterBackgroungColor"
                           name="footer_background_color" value="{{ $site->footer_background_color }}">
                </div>
            </div>
            <div class="row" id="comfort_button_color">
                <div class="col-6">
                    <label for="sitesModalComfortButtonColor" class="form-label">Comfort Button Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalComfortButtonColor"
                           name="comfort_button_color" value="{{ $site->comfort_button_color }}">
                </div>
            </div>
            <div class="row" id="footer_button_color">
                <div class="col-6">
                    <label for="sitesModalFooterButtonColor" class="form-label">Footer Button Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalFooterButtonColor"
                           name="footer_button_color" value="{{ $site->footer_button_color }}">
                </div>
            </div>
            <div class="row" id="footer_text_color">
                <div class="col-6">
                    <label for="sitesModalFooterTextColor" class="form-label">Footer Text Color</label>
                </div>
                <div class="col-6">
                    <input class="form-control" data-jscolor="{format : 'hex'}"
                           id="sitesModalFooterTextColor"
                           name="footer_text_color" value="{{ $site->footer_text_color }}">
                </div>
            </div>
        </div>
    </div>
    <div class="float-end mt-5">
        <a href="{{ url('sites') }}">
            <button type="button" class="btn btn-secondary">Back</button>
        </a>
        @if(isset($site->id))
            <button type="button" class="btn btn-primary" id="btnLoanSiteUpdate">Update</button>
        @else
            <button type="button" class="btn btn-primary" id="btnLoanSiteCreate">Create</button>
        @endif
    </div>
</form>



