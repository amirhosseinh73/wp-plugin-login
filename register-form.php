<?php

function register_form_password_HTML() {
    $username = __( wp_unslash( $_POST['username'] ) );
    $first_name = __( wp_unslash( $_POST['first_name'] ) );
    $last_name = __( wp_unslash( $_POST['last_name'] ) );
    return "<form class='woocommerce-form woocommerce-form-register ahnj-register-form' method='post'>

        " . do_action( 'woocommerce_register_form_start' ) . "

        <input type='hidden' name='username' value='$username' />
        <input type='hidden' name='first_name' value='$first_name' />
        <input type='hidden' name='last_name' value='$last_name' />

        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide'>
            <label class='ahnj-form-label' for='password'>
                " . $MESSAGES['insert_verif_code'] . "
                <span class='required'>*</span>
            </label>
            <input class='woocommerce-Input woocommerce-Input--text input-text ahnj-input-text'
            type='text'
            name='password'
            id='password'
            autocomplete='password' />
        </p>

        " . do_action( 'woocommerce_register_form' ) . "

        <p class='form-row'>
            " . wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ) . "
            <button
            class='woocommerce-button button woocommerce-form-register__submit ahnj-btn-submit'
            type='submit'
            name='login'>
            " . __( 'Log in', 'woocommerce' ) . "
            </button>
        </p>

        " . do_action( 'woocommerce_register_form_end' ) . "

    </form>";
}

function register_form_username_HTML() {
    global $form_error_register, $firstname, $lastname;

    $firstname = ( exists( $_POST['firstname'] ) ) ? __( wp_unslash( $firstname ) ) : '';
    $lastname = ( exists( $_POST['lastname'] ) ? __( wp_unslash( $last_name ) ) : '' );

    return "<form action='" . get_permalink() . "' method='post'
        class='woocommerce-form woocommerce-form-register register d-flex justify-content-end flex-wrap'>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-12'>
            <label for='reg_username' class='custom-label-box-shadow'>
            ' . __( 'Phone Number', 'woocommerce' ) . '&nbsp;<span class='required'>*</span>
            </label>
            <input type='tel' class='woocommerce-Input woocommerce-Input--text input-text custom-input-box-shadow'
            name='username' id='reg_username' autocomplete='username'
            value='' />
        </p>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-12 col-sm-6'>
            <label for='register_first_name' class='custom-label-box-shadow'>
            ' . __( 'First name', 'woocommerce' ) . '&nbsp;<span class='required'>*</span>
            </label>
            <input type='text' class='woocommerce-Input woocommerce-Input--text input-text custom-input-box-shadow'
            name='firstname' id='register_first_name' autocomplete='firstname'
            value='$firstname' />
        </p>
        <p class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide col-12 col-sm-6'>
            <label for='reg_last_name' class='custom-label-box-shadow'>
            ' . __( 'Last name', 'woocommerce' ) . '&nbsp;<span class='required'>*</span>
            </label>
            <input type='text' class='woocommerce-Input woocommerce-Input--text input-text custom-input-box-shadow'
            name='last_name' id='reg_last_name' autocomplete='last_name'
            value='$lastname' />
        </p>
        <p class='woocommerce-form-row form-row col-8 col-sm-5'>
            <button type='submit' class='woocommerce-Button woocommerce-button button woocommerce-form-register__submit mx-0 btn-block w-100'
            name='amhnj_create_new_customer' value='' . __( 'Register', 'woocommerce' ) . ''>
                ' . __( 'Register', 'woocommerce' ) . '
            </button>
        </p>
    </form>";
}

function render_register_form() {
    global $phone_number, $form_error, $first_name, $last_name;

    $is_username_submitted = (isset( $_POST['ahnj_register_username'] ) && exists( $_POST['username'] ) && count( $form_error->get_error_messages() ) < 1);

    if ( $is_username_submitted )
        echo register_form_password_HTML();
    else
        echo 
}

function validate_form( $phone_number, $first_name, $last_name ) {
 
    // Make the WP_Error object global    
    global $form_error;
     
    // If any field is left empty, add the error message to the error object
    if ( empty( $phone_number ) || ! is_numeric( $phone_number ) || strlen( $phone_number ) !== 11 || $phone_number[ 0 ] !== "0" || $phone_number[ 1 ] !== "9" ) {
        $form_error->add( 'registration-error-invalid-username', __( 'شماره تلفن وارد شده صحیح نیست!', 'woocommerce' ) );
    }

    if ( empty( $first_name ) ) {
        $form_error->add( 'registration-error-invalid-firstname', __( 'لطفا نام خود را وارد نمایید!', 'woocommerce' ) );
    }

    if ( empty( $last_name ) ) {
        $form_error->add( 'registration-error-invalid-lastname', __( 'لطفا نام خانوادگی خود را وارد نمایید!', 'woocommerce' ) );
    }

    if ( username_exists( $phone_number ) ) {
        $form_error->add( 'registration-error-username-exists', __( 'شماره وارد شده قبلا ثبت شده است!', 'woocommerce' ) );
    }

}

function send_sms_register( $phone_number, $password ) {
    global $form_error;
     
    if ( 1 > count( $form_error->get_error_messages() ) ) {
             
        send_sms_ir( "register", $phone_number, $password );
 
    }
}

function register_customer( $phone_number, $password, $first_name, $last_name ) {
    global $form_error;

    if ( 1 <= count( $form_error->get_error_messages() ) ) return;

    $email = "$phone_number@luxstars.ir";

    if ( username_exists( $phone_number ) || email_exists( $email ) ) {
        return $form_error->add( 'registration-error-username-exists', __( 'شماره وارد شده قبلا ثبت شده است!', 'woocommerce' ) );
    }

    $userData = array(
        'user_login' => $phone_number,
        'user_pass'  => $password,
        'user_email' => $email,
        'display_name' => $first_name . " " . $last_name,
        'role'       => "customer",

        "first_name"        => $first_name,
        "billing_first_name" => $first_name,
        "last_name"          => $last_name,
        "billing_last_name"  => $last_name,
    );

    $customer_id = wp_insert_user( $userData );

    if ( is_wp_error( $customer_id ) ) {
        return $form_error->add( $customer_id );
    }

    do_action( 'woocommerce_created_customer', $customer_id, $userData, $password );

    return $customer_id;
}

function do_register_form() {
    global $form_error, $phone_number, $first_name, $last_name;
    $form_error = new WP_Error;

    if ( isset( $_POST['amhnj_create_new_customer'] ) ) {
        // Get the form data
        $phone_number = trim( $_POST['username'] );
        $first_name = trim( $_POST['first_name'] );
        $last_name = trim( $_POST['last_name'] );

        // validate the user form input
        validate_form( $phone_number, $first_name, $last_name );

        $random_code = random_verficiraion_code();
        // send the sms
        send_sms_register( $phone_number, $random_code );

        register_customer( $phone_number, $random_code, $first_name, $last_name );
    }

    // if $form_error is WordPress Error, loop through the error object
    // and echo the error
    if ( is_wp_error( $form_error ) ) {
        foreach ( $form_error->get_error_messages() as $error ) {
            echo "<p class='woocommerce-error'>$error</p>";
        }
    }
     
    // display the contact form
    register_form();
 
}

function register_form_shortcode() {
    ob_start();
    do_register_form();
    return ob_get_clean();
}
add_shortcode( 'amhnj_register_form', 'register_form_shortcode' );