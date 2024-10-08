document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-post');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-postid');
            const postElement = this.closest('.post');
            Swal.fire({
                title: "คุณแน่ใจหรือไม่?",
                text: "คุณจะไม่สามารถกู้คืนโพสต์นี้ได้!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "ใช่, ลบเลย!",
                cancelButtonText: "ยกเลิก"
            }).then((result) => {
                if (result.isConfirmed) {
                    // แสดง loading
                    Swal.fire({
                        title: 'กำลังลบโพสต์...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ส่งคำขอ AJAX เพื่อลบโพสต์
                    fetch('deletepost.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'postid=' + postId
                    })
                    .then(response => response.text())
                    .then(text => {
                        // ลบ HTML comments หรือ notices ที่อาจมีอยู่ในผลลัพธ์
                        const jsonStart = text.indexOf('{');
                        const jsonEnd = text.lastIndexOf('}');
                        if (jsonStart >= 0 && jsonEnd >= 0) {
                            text = text.substring(jsonStart, jsonEnd + 1);
                        }
                        return JSON.parse(text);
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "ลบแล้ว!",
                                text: "โพสต์ของคุณถูกลบเรียบร้อยแล้ว",
                                icon: "success"
                            }).then(() => {
                                postElement.remove();
                            });
                        } else {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบโพสต์');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: "เกิดข้อผิดพลาด!",
                            text: "เกิดข้อผิดพลาดในการลบโพสต์: " + error.message,
                            icon: "error"
                        });
                    });
                }
            });
        });
    });

    // เพิ่มโค้ดสำหรับอัปโหลดรูปโปรไฟล์
    const profileImageInput = document.getElementById('profile_image');
    const profileImageForm = document.getElementById('profile-image-form');

    profileImageInput.addEventListener('change', function() {
        const formData = new FormData(profileImageForm);
        
        // แสดง loading
        Swal.fire({
            title: 'กำลังอัปโหลดรูปภาพ...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('upload_profile_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "อัปโหลดสำเร็จ!",
                    text: "รูปโปรไฟล์ของคุณถูกอัปเดตแล้ว",
                    icon: "success"
                }).then(() => {
                    // อัปเดตรูปภาพโดยไม่ต้องรีโหลดหน้า
                    const profileImage = document.querySelector('.gradient-custom img');
                    if (profileImage) {
                        profileImage.src = 'uploads/' + data.filename + '?t=' + new Date().getTime();
                    } else {
                        console.error('Profile image element not found');
                    }
                });
            } else {
                throw new Error(data.message || 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: "เกิดข้อผิดพลาด!",
                text: error.message,
                icon: "error"
            });
        });
    });
});
