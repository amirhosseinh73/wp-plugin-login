<?php

add_shortcode( 'amhnj_login_form', 'login_form_shortcode' );
function login_form_shortcode() {
    ob_start();
    do_login_form();
    return ob_get_clean();
}

function do_login_form() {
    global $formErrorLogin, $phoneNumber;
    $formErrorLogin = new WP_Error;

    if ( isset( $_POST['am_login_submit_username'] ) ) {
        $phoneNumber = trim( $_POST['username'] );
        validate_login( $phoneNumber );

        $randomCode = random_verfication_code();
        // send the sms
        send_sms_login( $phoneNumber, $randomCode );
        change_password( $phoneNumber, $randomCode );

    }

    if ( is_wp_error( $formErrorLogin ) ) {
        foreach ( $formErrorLogin->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }

    render_login_form();
}

function validate_login( $phoneNumber ) {
    global $formErrorLogin, $_AM_MESSAGES;
     
    if ( ! validate_mobile($phoneNumber) ) $formErrorLogin->add( 'login-error-invalid-username', $_AM_MESSAGES["username_wrong_format"] );

    if ( ! username_exists($phoneNumber) ) $formErrorLogin->add( 'login-error-username-not-exists', $_AM_MESSAGES["username_not_found"] );
}

function change_password( $phoneNumber, $password ) {
    global $formErrorLogin;

    if ( count( $formErrorLogin->get_error_messages() ) >= 1 ) return;

    $userInfo = get_user_by( "login", $phoneNumber );
    
    wp_set_password( $password, $userInfo->ID );
}

function send_sms_login( $phoneNumber, $password ) {
    global $formErrorLogin;
     
    if ( count( $formErrorLogin->get_error_messages() ) >= 1 ) return;

    return send_sms_ir( "login", $phoneNumber, $password );
}

function render_login_form() {
    global $formErrorLogin;

    $is_username_submitted = (isset( $_POST['am_login_submit_username'] ) && exists( $_POST['username'] ) && count( $formErrorLogin->get_error_messages() ) < 1);

    if ( $is_username_submitted )
        echo login_form_password_HTML();
    else
        echo login_form_username_HTML();
}

function login_form_password_HTML() {
    global $_AM_MESSAGES;
    $username = __( wp_unslash( $_POST['username'] ) );
    return "<form class='woocommerce-form woocommerce-form-login am-login-form' method='post'>

        " . do_action( 'woocommerce_login_form_start' ) . "

        <input type='hidden' name='username' value='$username' />

        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label for='password' class='am-form-label'>
                " . $_AM_MESSAGES['insert_verif_code'] . "
                <span class='required'>*</span>
            </label>
            <input class='woocommerce-Input woocommerce-Input--text input-text am-input-text'
            type='text'
            name='password'
            id='password' autocomplete='password' />
        </p>

        " . do_action( 'woocommerce_login_form' ) . "

        <p class='form-row'>
            " . wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ) . "
            <button class='woocommerce-button button woocommerce-form-login__submit am-btn-submit'
            type='submit'
            name='login'>
            " . __( 'Log in', 'woocommerce' ) . "
            </button>
        </p>

        " . do_action( 'woocommerce_login_form_end' ) . "

    </form>";
}

function login_form_username_HTML() {
    return '<form action="' . get_permalink() . '" method="post"
        class="woocommerce-form woocommerce-form-login am-login-form">
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="username" class="custom-label-box-shadow">
                ' . __( 'Phone Number', 'woocommerce' ) . '
                <span class="required">*</span>
            </label>
            <input class="woocommerce-Input woocommerce-Input--text input-text am-input-text"
            type="text"
            name="username"
            id="username"
            autocomplete="username"/>
        </p>
        <p class="woocommerce-form-row form-row">
            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-login__submit am-btn-submit"
            name="am_login_submit_username">
                ' . __( 'Log in', 'woocommerce' ) . '
            </button>
        </p>
    </form>';
}