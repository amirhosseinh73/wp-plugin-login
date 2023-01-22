<?php

function login_form_password_HTML() {
    $username = __( wp_unslash( $_POST['username'] ) );
    return "<form class='woocommerce-form woocommerce-form-login ahnj-login-form' method='post'>

        " . do_action( 'woocommerce_login_form_start' ) . "

        <input type='hidden' name='username' value='$username' />

        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label for='password' class='ahnj-form-label'>
                " . $MESSAGES['insert_verif_code'] . "
                <span class='required'>*</span>
            </label>
            <input class='woocommerce-Input woocommerce-Input--text input-text ahnj-input-text'
            type='text'
            name='password'
            id='password' autocomplete='password' />
        </p>

        " . do_action( 'woocommerce_login_form' ) . "

        <p class='form-row'>
            " . wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ) . "
            <button class='woocommerce-button button woocommerce-form-login__submit ahnj-btn-submit'
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
        class="woocommerce-form woocommerce-form-login ahnj-login-form">
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="username" class="custom-label-box-shadow">
                ' . __( 'Phone Number', 'woocommerce' ) . '
                <span class="required">*</span>
            </label>
            <input class="woocommerce-Input woocommerce-Input--text input-text ahnj-input-text"
            type="text"
            name="username"
            id="username"
            autocomplete="username"/>
        </p>
        <p class="woocommerce-form-row form-row">
            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit ahnj-btn-submit"
            name="ahnj_login_submit_username">
                ' . __( 'Log in', 'woocommerce' ) . '
            </button>
        </p>
    </form>';
}

function render_login_form() {//pt114/6137/s1.fox1.ml/123456
    global $form_error_login;

    $is_username_submitted = (isset( $_POST['ahnj_login_submit_username'] ) && exists( $_POST['username'] ) && count( $form_error_login->get_error_messages() ) < 1);

    if ( $is_username_submitted )
        echo login_form_password_HTML();
    else
        echo login_form_username_HTML();
}

function validate_login( $phone_number ) {
    global $form_error_login;
     
    if ( ! validate_mobile($phone_number) ) $form_error_login->add( 'login-error-invalid-username', $MESSAGES["username_wrong_format"] );

    if ( ! username_exists($phone_number) ) $form_error_login->add( 'login-error-username-not-exists', $MESSAGES["username_not_found"] );
}

function send_sms_login( $phone_number, $password ) {
    global $form_error_login;
     
    if ( count( $form_error_login->get_error_messages() ) >= 1 ) return;

    return send_sms_ir( "login", $phone_number, $password );
}

function change_password( $phone_number, $password ) {
    global $form_error_login;

    if ( count( $form_error_login->get_error_messages() ) >= 1 ) return;

    $userInfo = get_user_by( "login", $phone_number );
    
    wp_set_password( $password, $userInfo->ID );
}

function do_login_form() {
    global $form_error_login, $phone_number;
    $form_error_login = new WP_Error;

    if ( isset( $_POST['ahnj_login_submit_username'] ) ) {
        $phone_number = trim( $_POST['username'] );

        validate_login( $phone_number );

        $random_code = random_verfication_code();

        // send the sms
        send_sms_login( $phone_number, $random_code );

        change_password( $phone_number, $random_code );
    }

    if ( is_wp_error( $form_error_login ) ) {
        foreach ( $form_error_login->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }

    render_login_form();
}

function login_form_shortcode() {
    ob_start();
    do_login_form();
    return ob_get_clean();
}
add_shortcode( 'amhnj_login_form', 'login_form_shortcode' );