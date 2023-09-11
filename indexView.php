<div class="custom-page-content">
    <div id="myDIV" class="header">
        <h2 style="margin:5px">My To Do List</h2>
        <input type="text" id="myInput" placeholder="Title...">
        <span onclick="newElement()" class="addBtn">Add</span>
    </div>
    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';
    $data = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id = $user_id");
    echo '<ul id="myUL">';
    if ($data) {
        foreach ($data as $item) {
            if ($item->status == 1) {
                echo '<li class="checked">' . esc_html($item->title) . ': ' . esc_html($item->description) . '</li>';
            } else {
                echo '<li>' . esc_html($item->title) . ': ' . esc_html($item->description) . '<span onclick="updateTaskStatus('.$item->id.' , this)">Done</span></li>';
            }
        }
    } else {
        echo '<p id="emptyTable" class="d-block">table is empty</p>';
    }
    echo '</ul>';

    ?>
    <script>
        // Create a "close" button and append it to each list item
        var myNodelist = document.getElementsByTagName("LI");
        var i;
        for (i = 0; i < myNodelist.length; i++) {
            var span = document.createElement("SPAN");
            var txt = document.createTextNode("\u00D7");
            span.className = "close";
            span.appendChild(txt);
            myNodelist[i].appendChild(span);
        }

        // Click on a close button to hide the current list item
        var close = document.getElementsByClassName("close");
        var i;
        for (i = 0; i < close.length; i++) {
            close[i].onclick = function () {
                var div = this.parentElement;
                div.style.display = "none";
            }
        }

        // Add a "checked" symbol when clicking on a list item
        var list = document.querySelector('ul');
        list.addEventListener('click', function (ev) {
            if (ev.target.tagName === 'LI') {
                ev.target.classList.toggle('checked');
            }
        }, false);

        // Create a new list item when clicking on the "Add" button
        function newElement() {
            // var li = document.createElement("li");
            // var inputValue = document.getElementById("myInput").value;
            // var t = document.createTextNode(inputValue);
            // li.appendChild(t);
            // if (inputValue === '') {
            //     alert("You must write something!");
            // } else {
            //     document.getElementById("myUL").appendChild(li);
            // }
            // document.getElementById("myInput").value = "";
            //
            // var span = document.createElement("SPAN");
            // var txt = document.createTextNode("\u00D7");
            // span.className = "close";
            // span.appendChild(txt);
            // li.appendChild(span);
            //
            // for (i = 0; i < close.length; i++) {
            //     close[i].onclick = function () {
            //         var div = this.parentElement;
            //         div.style.display = "none";
            //     }
            // }
            var inputValue = document.getElementById("myInput").value;
            if (inputValue === '') {
                alert("شما باید چیزی وارد کنید!");
                return;
            }

            // ارسال داده به دیتابیس با استفاده از AJAX
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_data_to_database', // نام اکشن وردپرس برای اجرای درخواست
                    data_value: inputValue
                },
                success: function(response) {
                    if (response === 'success') {
                        // اگر ذخیره کردن در دیتابیس موفقیت‌آمیز بود، متن را به لیست اضافه کنید
                        var li = document.createElement("li");
                        var t = document.createTextNode(inputValue);
                        li.appendChild(t);
                        document.getElementById("myUL").appendChild(li);
                        document.getElementById("myInput").value = "";
                    } else {
                        alert("خطا در ذخیره داده در دیتابیس.");
                    }
                }
            });
        }
        var list = document.querySelector('ul');
        list.addEventListener('click', function (ev) {
            if (ev.target.tagName === 'LI') {
                ev.target.classList.toggle('checked');
                var taskId = ev.target.getAttribute('data-task-id');
                updateTaskStatus(taskId , el);
            }
        }, false);
        function updateTaskStatus(taskId , el) {
            el.remove()
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'update_task_status',
                    task_id: taskId
                },
                success: function (response) {
                    if (response === 'success') {
                        // Remove the "Done" element from the list item
                        var listItem = document.querySelector('li[data-task-id="' + taskId + '"]');
                        var doneElement = listItem.querySelector('p');
                        if (doneElement) {
                            listItem.removeChild(doneElement);
                        }
                    } else {
                        alert("خطا در بروزرسانی وضعیت وظیفه.");
                    }
                }
            });
        }
    </script>
</div>
