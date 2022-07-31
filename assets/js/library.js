var xhr;

function run(urls, form, tombol, modal, loading, appends, datatables, pesan) {
    if (xhr && xhr.readystate != 4) {
        xhr.abort();
    }
    $(loading).append(
        "<div class='spinner-border spinner-border-sm text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
    );
    $(tombol).prop('disabled', true);
    xhr = $.ajax({
        type: "POST",
        url: urls,
        // data: $(form).serialize(),
        data: new FormData(document.getElementById(form)),
        processData: false,
        cache: false,
        contentType: false,
        success: function (response) {
            $(loading).html('');
            Swal.fire(
                'Sukses!',
                pesan,
                'success'
            );
            $(tombol).prop('disabled', false);
            $(modal).modal('toggle');
            $(appends).html(response);
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $(datatables).DataTable().destroy();
            $(datatables).DataTable();
        },
        error: function () {
            $(loading).html('');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
            $(tombol).prop('disabled', false);
            $(modal).modal('toggle');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        }
    });
}


function confirmdelete(e, link, pesan, appends, loading, tombol, datatables) {
    if (xhr && xhr.readystate != 4) {
        xhr.abort();
    }
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        html: pesan,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $(loading).append(
                "<div class='spinner-border spinner-border-sm text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>"
            );
            $(tombol).prop('disabled', true);
            $(tombol).addClass("disabled");
            xhr = $.ajax({
                url: link,
                type: "get",
                success: function (response) {
                    $(loading).html('');
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    );
                    $(tombol).prop('disabled', false);
                    $(tombol).removeClass("disabled");
                    $(appends).html(response);
                    $(datatables).DataTable().destroy();
                    $(datatables).DataTable();
                },
                error: function (xhr) {
                    $(loading).html('');
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!'
                    });
                    $(tombol).prop('disabled', false);
                    $(tombol).removeClass("disabled");
                }
            });
        }
    })
}

// $(document).ready(function() {
//     (function() {
//         'use strict'
//         var forms = document.querySelectorAll('.needs-validation')
//         Array.prototype.slice.call(forms)
//             .forEach(function(form) {
//                 var base = document.querySelector('.validasi-form');
//                 base.addEventListener('click', function(event) {
//                     if (!form.checkValidity()) {
//                         event.preventDefault()
//                         event.stopPropagation()
//                     }
//                     form.classList.add('was-validated')
//                 }, false)
//             })
//     })()
// });


$.htmlentities = {
    /**
     * Converts a string to its html characters completely.
     * It's equivalent to htmlentities() in PHP
     * Reference: https://css-tricks.com/snippets/javascript/htmlentities-for-javascript/
     *
     * @param {String} str String with unescaped HTML characters
     **/
    encode (str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    },
    /**
     * Converts an html characterSet into its original character.
     * It's equivalent to html_entity_decode() in PHP
     * Reference: https://stackoverflow.com/questions/5796718/html-entity-decode
     *
     * @param {string}
     * @return {string}
     **/
    decode: (() => {
      // this prevents any overhead from creating the object each time
      let element = document.createElement('div');
  
      function decodeHTMLEntities(str) {
        if (str && typeof str === 'string') {
          // strip script/html tags
          str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
          str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
          element.innerHTML = str;
          str = element.textContent;
          element.textContent = '';
        }
  
        return str;
      }
  
      return decodeHTMLEntities;
    })()
  };