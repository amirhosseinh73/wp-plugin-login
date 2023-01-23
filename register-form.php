<?php

add_shortcode( 'amhnj_register_form', 'register_form_shortcode' );
function register_form_shortcode() {
    ob_start();
    do_register_form();
    return ob_get_clean();
}

function do_register_form() {
    global $formErrorRegister, $phoneNumber, $firstname, $lastname;
    $formErrorRegister = new WP_Error;

    if ( isset( $_POST['am_register_submit_username'] ) ) {
        // Get the form data
        $phoneNumber = trim( $_POST['username'] );
        $firstname = trim( $_POST['firstname'] );
        $lastname = trim( $_POST['lastname'] );

        // validate the user form input
        validate_register( $phoneNumber, $firstname, $lastname );

        $randomCode = random_verfication_code();
        // send the sms
        send_sms_register( $phoneNumber, $randomCode );
        register_customer( $phoneNumber, $randomCode, $firstname, $lastname );
    }

    if ( is_wp_error( $formErrorRegister ) ) {
        foreach ( $formErrorRegister->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }

    render_register_form();
}

function validate_register( $phoneNumber, $firstname, $lastname ) {
    global $formErrorRegister, $_AM_MESSAGES;
     
    if ( ! validate_mobile( $phoneNumber ) ) $formErrorRegister->add( 'register-error-invalid-username', $_AM_MESSAGES["username_wrong_format"] );
    if ( username_exists( $phoneNumber ) ) $formErrorRegister->add( 'register-error-username-exists', $_AM_MESSAGES["username_duplicate"] );

    if ( empty( $firstname ) ) $formErrorRegister->add( 'register-error-invalid-firstname', $_AM_MESSAGES["insert_firstname"] );
    if ( empty( $lastname ) ) $formErrorRegister->add( 'register-error-invalid-lastname', $_AM_MESSAGES["insert_lastname"] );
}

function send_sms_register( $phoneNumber, $password ) {
    global $formErrorRegister;
     
    if ( count( $formErrorRegister->get_error_messages() ) < 1 ) send_sms_ir( "register", $phoneNumber, $password );
}

function register_customer( $phoneNumber, $password, $firstname, $lastname ) {
    global $formErrorRegister;

    if ( count( $formErrorRegister->get_error_messages() ) >= 1 ) return;

    $email = "$phoneNumber@" . home_url();

    $userData = array(
        'user_login' => $phoneNumber,
        'user_pass'  => $password,
        'user_email' => $email,
        'display_name' => $firstname . " " . $lastname,
        'role'       => "customer",

        "first_name"        => $firstname,
        "billing_first_name" => $firstname,
        "last_name"          => $lastname,
        "billing_last_name"  => $lastname,
    );

    $customerID = wp_insert_user( $userData );

    if ( is_wp_error( $customerID ) ) return $formErrorRegister->add( $customerID );

    do_action( 'woocommerce_created_customer', $customerID, $userData, $password );

    return $customerID;
}

function render_register_form() {
    global $form_error_register;

    $is_username_submitted = (isset( $_POST['am_register_username'] ) && exists( $_POST['username'] ) && count( $form_error_register->get_error_messages() ) < 1);

    if ( $is_username_submitted )
        echo register_form_password_HTML();
    else
        echo register_form_username_HTML();
}

function register_form_password_HTML() {
    global $_AM_MESSAGES;

    $username = __( wp_unslash( $_POST['username'] ) );
    $first_name = __( wp_unslash( $_POST['first_name'] ) );
    $last_name = __( wp_unslash( $_POST['last_name'] ) );

    return "<form class='woocommerce-form woocommerce-form-register am-register-form' method='post'>

        " . do_action( 'woocommerce_register_form_start' ) . "

        <input type='hidden' name='username' value='$username' />
        <input type='hidden' name='first_name' value='$first_name' />
        <input type='hidden' name='last_name' value='$last_name' />

        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label class='am-form-label' for='password'>
                " . $_AM_MESSAGES['insert_verif_code'] . "
                <span class='required'>*</span>
            </label>
            <input class='woocommerce-Input woocommerce-Input--text input-text am-input-text'
            type='text'
            name='password'
            id='password'
            autocomplete='password' />
        </p>

        " . do_action( 'woocommerce_register_form' ) . "

        <p class='form-row'>
            " . wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ) . "
            <button
            class='woocommerce-button button woocommerce-form-register__submit am-btn-submit'
            type='submit'
            name='login'>
            " . __( 'Log in', 'woocommerce' ) . "
            </button>
        </p>

        " . do_action( 'woocommerce_register_form_end' ) . "

    </form>";
}

function register_form_username_HTML() {
    global $firstname, $lastname;

    $firstname = ( exists( $_POST['firstname'] ) ? __( wp_unslash( $firstname ) ) : '' );
    $lastname = ( exists( $_POST['lastname'] ) ? __( wp_unslash( $lastname ) ) : '' );

    return "<form action='" . get_permalink() . "' method='post'
        class='woocommerce-form woocommerce-form-register am-register-form'>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label for='username' class='am-form-label'>
                " . __( 'Phone Number', 'woocommerce' ) . "
                <span class='required'>*</span>
            </label>
            <input type='tel' class='woocommerce-Input woocommerce-Input--text input-text am-input-text'
                name='username'
                id='username'
                autocomplete='username'
            />
        </p>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label for='firstname' class='am-form-label'>
                " . __( 'First name', 'woocommerce' ) . "
                <span class='required'>*</span>
            </label>
            <input type='text' class='woocommerce-Input woocommerce-Input--text input-text am-input-text'
                name='firstname'
                id='firstname'
                autocomplete='firstname'
                value='$firstname'
            />
        </p>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label for='lastname' class='am-form-label'>
                " . __( 'Last name', 'woocommerce' ) . "
                <span class='required'>*</span>
            </label>
            <input type='text' class='woocommerce-Input woocommerce-Input--text input-text am-input-text'
                name='lastname'
                id='lastname'
                autocomplete='lastname'
                value='$lastname'
            />
        </p>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <button type='submit' class='woocommerce-Button woocommerce-button button woocommerce-form-register__submit am-btn-submit'
                name='am_register_submit_username'>
                " . __( 'Register', 'woocommerce' ) . "
            </button>
        </p>
    </form>";
}