import $ from 'jquery';
window.$ = $;

// Import jQuery Validation
import 'jquery-validation';

$(document).ready(function() {


    $('#date_range').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#custom-date-range').removeClass('hidden');
        } else {
            $('#custom-date-range').addClass('hidden');
        }
    });

    $("#item_hsn_code, #item_price").on('keypress', function(e) {
        const key = e.keyCode || e.which;
        if(key < 48 || key > 57) {
            if($(this).attr('id') === 'item_price' && key === 46){
                // Allow decimal point in item_price
                return true;
            }
            e.preventDefault();
        }
    });

    $("#client_name").on('change', function () {
        $("#display_name").val($(this).val().trim());
    })



    $("#itemForm").validate({
        rules: {
            item_type: {
                required: true
            },
            item_name: {
                required: true,
                minlength: 3
            },
            item_hsn_code: {
                required: true,
                digits: true,
                minlength: 4
            },
            item_price: {
                required: true,
                number: true,
                min: 0
            }
        },
        messages: {
            item_name: {
                required: "Please enter the item name",
                minlength: "Item name must be at least 3 characters long"
            },
            item_hsn_code: {
                required: "Please enter the HSN code",
                digits: "HSN code must be numeric",
                minlength: "HSN code must be at least 4 digits long"
            },
            item_price: {
                required: "Please enter the price",
                number: "Price must be a valid number",
                min: "Price cannot be negative"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Form is valid, you can submit it
            form.submit();
        }
    });

    $("#clientForm").validate({
        rules: {
            client_name: {
                required: true,
                minlength: 3
            },
            client_email: {
                email: true
            },
            client_type: {
                required: true
            }
        },
        messages: {
            client_name: {
                required: "Please enter the client's name",
                minlength: "Client's name must be at least 3 characters long"
            },
            client_email: {
                email: "Please enter a valid email address"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Form is valid, you can submit it
            form.submit();
        }
    });

    $("#salesForm").validate({
        rules: {
            client_id: {
                required: true
            },
            sale_date: {
                required: true,
                date: true
            },
            sales_type: {
                required: true
            },
            base_amount: {
                required: true,
                number: true,
                min: 0.01
            },
            tax_rate: {
                required: function(element) {
                    return $('#sales_type').val() === 'invoice';
                },
                number: true,
                min: 0,
                max: 100
            },
            tax_amount: {
                number: true,
                min: 0
            },
            tds_rate: {
                number: true,
                min: 0,
                max: 100
            },
            tds: {
                number: true,
                min: 0
            },
            total_amount: {
                required: true,
                number: true,
                min: 0
            },
            notes: {
                maxlength: 1000
            }
        },
        messages: {
            client_id: {
                required: "Please select a client"
            },
            sale_date: {
                required: "Please select a sale date",
                date: "Please enter a valid date"
            },
            sales_type: {
                required: "Please select a sale type"
            },
            base_amount: {
                required: "Please enter the base amount",
                number: "Base amount must be a valid number",
                min: "Base amount must be greater than 0"
            },
            tax_rate: {
                required: "Please select a tax rate for invoice sales",
                number: "Tax rate must be a valid number",
                min: "Tax rate cannot be negative",
                max: "Tax rate cannot exceed 100%"
            },
            tax_amount: {
                number: "Tax amount must be a valid number",
                min: "Tax amount cannot be negative"
            },
            tds_rate: {
                number: "TDS rate must be a valid number",
                min: "TDS rate cannot be negative",
                max: "TDS rate cannot exceed 100%"
            },
            tds: {
                number: "TDS amount must be a valid number",
                min: "TDS amount cannot be negative"
            },
            total_amount: {
                required: "Total amount is required",
                number: "Total amount must be a valid number",
                min: "Total amount cannot be negative"
            },
            notes: {
                maxlength: "Notes cannot exceed 1000 characters"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Additional validation for cash vs invoice
            const salesType = $('#sales_type').val();
            const taxRate = $('#tax_rate').val();

            if (salesType === 'invoice' && (!taxRate || taxRate === '')) {
                alert('Please select a tax rate for invoice sales');
                return false;
            }

            // Form is valid, you can submit it
            form.submit();
        }
    });

    $("#employeeForm").validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            last_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            gender: {
                required: true
            },
            mobile_number: {
                digits: true,
                required: false
            },
            department: {
                required: true
            },
            designation: {
                maxlength: 100
            },
            salary: {
                required: true,
                number: true,
                min: 1
            },
            salary_hours: {
                number: true,
                min: 1,
                max: 24
            },
            joining_date: {
                required: true,
                date: true
            },
            status: {
                required: true
            },
            leaving_date: {
                required: function(element) {
                    return $('#status').val() === 'inactive';
                },
                date: true
            }
        },
        messages: {
            first_name: {
                required: "Please enter the first name",
                minlength: "First name must be at least 2 characters long",
                maxlength: "First name cannot exceed 50 characters"
            },
            last_name: {
                required: "Please enter the last name",
                minlength: "Last name must be at least 2 characters long",
                maxlength: "Last name cannot exceed 50 characters"
            },
            gender: {
                required: "Please select a gender"
            },
            mobile_number: {
                digits: "Mobile number must contain only digits",
                minlength: "Mobile number must be exactly 10 digits",
                maxlength: "Mobile number must be exactly 10 digits"
            },
            department: {
                required: "Please select a department"
            },
            designation: {
                maxlength: "Designation cannot exceed 100 characters"
            },
            salary: {
                required: "Please enter the monthly salary",
                number: "Salary must be a valid number",
                min: "Salary must be greater than 0"
            },
            salary_hours: {
                number: "Working hours must be a valid number",
                min: "Working hours must be at least 1",
                max: "Working hours cannot exceed 24"
            },
            joining_date: {
                required: "Please select the joining date",
                date: "Please enter a valid date"
            },
            status: {
                required: "Please select employee status"
            },
            leaving_date: {
                required: "Please select the leaving date for inactive employees",
                date: "Please enter a valid date"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Additional validation for inactive employees
            const status = $('#status').val();
            const leavingDate = $('#leaving_date').val();
            const joiningDate = $('#joining_date').val();

            if (status === 'inactive' && (!leavingDate || leavingDate === '')) {
                alert('Please select a leaving date for inactive employees');
                return false;
            }

            // Check if leaving date is after joining date
            if (status === 'inactive' && joiningDate && leavingDate) {
                if (new Date(leavingDate) <= new Date(joiningDate)) {
                    alert('Leaving date must be after joining date');
                    return false;
                }
            }

            // Check for duplicate mobile number (this would need AJAX in real implementation)
            // For now, just basic validation

            // Form is valid, you can submit it
            form.submit();
        }
    });

    $("#expenseForm").validate({
        rules: {
            expense_type: {
                required: true
            },
            expense_date: {
                required: true,
                date: true
            },
            base_amount: {
                required: true,
                number: true,
                min: 0.01
            },
            tax_rate: {
                required: function(element) {
                    return $('#expense_type').val() === 'invoice';
                },
                number: true,
                min: 0,
                max: 100
            },
            tax_amount: {
                number: true,
                min: 0
            },
            total_amount: {
                required: true,
                number: true,
                min: 0
            },
            paid: {
                required: true
            },
            notes: {
                maxlength: 1000
            }
        },
        messages: {
            expense_type: {
                required: "Please select an expense type"
            },
            expense_date: {
                required: "Please select an expense date",
                date: "Please enter a valid date"
            },
            base_amount: {
                required: "Please enter the base amount",
                number: "Base amount must be a valid number",
                min: "Base amount must be greater than 0"
            },
            tax_rate: {
                required: "Please select a tax rate for invoice expenses",
                number: "Tax rate must be a valid number",
                min: "Tax rate cannot be negative",
                max: "Tax rate cannot exceed 100%"
            },
            tax_amount: {
                number: "Tax amount must be a valid number",
                min: "Tax amount cannot be negative"
            },
            total_amount: {
                required: "Total amount is required",
                number: "Total amount must be a valid number",
                min: "Total amount cannot be negative"
            },
            paid: {
                required: "Please select a payment status"
            },
            payment_date: {
                required: "Please select a payment date for paid expenses",
                date: "Please enter a valid date"
            },
            notes: {
                maxlength: "Notes cannot exceed 1000 characters"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Additional validation for cash vs invoice
            const expenseType = $('#expense_type').val();
            const taxRate = $('#tax_rate').val();

            if (expenseType === 'invoice' && (!taxRate || taxRate === '')) {
                alert('Please select a tax rate for invoice expenses');
                return false;
            }

            // Form is valid, you can submit it
            form.submit();
        }
    });


    $("#paymentForm").validate({
        rules: {
            client_id: {
                required: true
            },
            payment_date: {
                required: true,
                date: true
            },
            amount_paid: {
                required: true,
                number: true,
                min: 0.01
            },
            payment_method: {
                required: true
            },
            notes: {
                maxlength: 1000
            }
        },
        messages: {
            client_id: {
                required: "Please select a client"
            },
            payment_date: {
                required: "Please select a payment date",
                date: "Please enter a valid date"
            },
            amount_paid: {
                required: "Please enter the payment amount",
                number: "Payment amount must be a valid number",
                min: "Payment amount must be greater than 0"
            },
            payment_method: {
                required: "Please select a payment method"
            },
            notes: {
                maxlength: "Notes cannot exceed 1000 characters"
            }
        },
        highlight: function(element) {
            $(element).parent().addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).parent().removeClass('is-invalid');
        },
        errorPlacement: errorPlacement,
        submitHandler: function(form) {
            // Form is valid, you can submit it
            form.submit();
        }
    });
})

function errorPlacement(error, element) {
    error.addClass('invalid-feedback');
    if (element.prop('type') === 'select-one') {
        error.insertAfter(element.parent().parent());
    } else {
        error.insertAfter(element.parent());
    }
}
