/**
 * =======================================
 * DIKELOLAIN - VCMS ADMIN JAVASCRIPT
 * =======================================
 * Virtual Content Management System
 * Edit content directly on visual preview
 */

(function ($) {
    "use strict";

    // =======================================
    // VCMS Object
    // =======================================
    const VCMS = {
        modal: null,
        currentEditItem: null,
        isSaving: false,

        init: function () {
            this.modal = $("#vcmsEditModal");
            this.isSaving = false;
            this.bindEvents();
            this.initSidebar();
            console.log("VCMS Admin Initialized");
        },

        // =======================================
        // BIND EVENTS
        // =======================================
        bindEvents: function () {
            const self = this;

            // Edit Button Click
            $(document).on("click", ".vcms-edit-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();
                const editType = $(this).data("edit-type");
                const section = $(this).closest(".vcms-editable-section");
                self.openEditModal(editType, section);
            });

            // Small Edit Indicator Click
            $(document).on("click", ".vcms-edit-indicator-small", function (e) {
                e.preventDefault();
                e.stopPropagation();
                const item = $(this).closest(".vcms-editable-item");
                const editType = item.data("edit-type");
                const itemId = item.data("item-id");
                self.openEditModal(editType, item);
            });

            // Regular Edit Indicator Click (for service detail, etc.)
            // Skip if click originated from a button inside (handled by .vcms-edit-btn handler)
            $(document).on("click", ".vcms-edit-indicator", function (e) {
                // If clicked on button or inside button, let button handler handle it
                if ($(e.target).closest(".vcms-edit-btn").length > 0) {
                    return;
                }
                
                e.preventDefault();
                e.stopPropagation();
                const item = $(this).closest(".vcms-editable-item");
                
                // Only proceed if item exists
                if (item.length === 0) {
                    return;
                }
                
                const editType = item.data("edit-type");
                self.openEditModal(editType, item);
            });

            // Close Modal
            $(".vcms-modal-close, .vcms-edit-modal-overlay").on(
                "click",
                function () {
                    self.closeEditModal();
                }
            );

            // Cancel Button
            $(".btn-cancel").on("click", function () {
                self.closeEditModal();
            });

            // Save Button
            $(".btn-save-modal").on("click", function (e) {
                e.preventDefault();
                if (!self.isSaving) {
                    self.saveChanges();
                }
            });

            // Save All Changes Button
            $(".btn-save-changes").on("click", function () {
                self.saveAllChanges();
            });

            // Prevent modal close when clicking inside modal content
            $(".vcms-edit-modal-content").on("click", function (e) {
                e.stopPropagation();
            });

            // =======================================
            // FILE UPLOAD EVENTS
            // =======================================
            
            // File input change - show preview
            $(document).on("change", ".vcms-file-input", function () {
                self.handleFilePreview(this);
            });

            // Main file input for image-link form
            $(document).on("change", "#vcms-file-input-main", function () {
                self.handleMainFilePreview(this);
            });

            // Remove new image button
            $(document).on("click", "#vcms-remove-new-image", function () {
                $("#vcms-file-input-main").val("");
                $("#vcms-new-image-preview").hide();
            });

            // Delete image button in gallery
            $(document).on("click", ".vcms-btn-delete-image", function (e) {
                e.preventDefault();
                const index = $(this).data("index");
                self.handleDeleteImage(index, $(this).closest(".vcms-image-item"));
            });

            // Delete service button
            $(document).on("click", ".vcms-delete-service-btn", function (e) {
                e.preventDefault();
                const serviceId = $(this).data("service-id");
                const index = $(this).data("index");
                self.deleteService(serviceId, index);
            });

            // Delete project button - handled via inline onclick in HTML

            // Project image file preview
            $(document).on("change", ".vcms-project-image-file", function () {
                const file = this.files[0];
                const previewId = $(this).data("preview");
                if (file && previewId) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("#" + previewId).attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Service background image file preview (for addNewService form)
            $(document).on("change", ".vcms-service-bg-file", function () {
                const file = this.files[0];
                const serviceId = $(this).data("service-id");
                if (file && serviceId) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("#service-bg-" + serviceId).attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Service image file preview (for service list and detail forms)
            $(document).on("change", ".vcms-service-image-file", function () {
                const file = this.files[0];
                const previewId = $(this).data("preview");
                if (file && previewId) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("#" + previewId).attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Client logo file preview
            $(document).on("change", ".vcms-client-logo-file", function () {
                const file = this.files[0];
                const previewId = $(this).data("preview");
                if (file && previewId) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $("#" + previewId).attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Reset to default button for single images
            $(document).on("click", ".vcms-btn-reset-to-default", function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $btn = $(this);
                const defaultImg = $btn.attr("data-default");
                const targetId = $btn.attr("data-target");
                const inputName = $btn.attr("data-input");
                
                console.log("Reset button clicked:", { defaultImg, targetId, inputName });
                
                if (confirm("Reset this image to default?")) {
                    // Update preview image
                    if (targetId) {
                        $("#" + targetId).attr("src", defaultImg);
                    }
                    
                    // Add hidden input to indicate reset
                    $('input[name="reset_' + (inputName || 'image') + '"]').remove();
                    $btn.closest(".vcms-form-group, .vcms-current-image-preview, div[style]").append(
                        '<input type="hidden" name="reset_' + (inputName || 'image_file') + '" value="' + defaultImg + '">'
                    );
                    
                    // Clear any file input
                    if (inputName) {
                        $('input[name="' + inputName + '"]').val('');
                        $('#' + inputName.replace('_', '-') + '-preview-container').hide();
                    }
                    
                    // Also clear the main file input
                    $('#vcms-file-input-main').val('');
                    $('#vcms-new-image-preview').hide();
                    
                    // Visual feedback
                    $btn.html('<i class="fa-solid fa-check"></i> Done!').css('background', '#4CAF50');
                    setTimeout(function() {
                        $btn.html('<i class="fa-solid fa-undo"></i> Reset to Default').css('background', '#ff4444');
                    }, 1500);
                    
                    console.log("Reset completed for:", inputName || 'image_file');
                }
            });

            // Reset gallery image to default
            $(document).on("click", ".vcms-btn-reset-gallery", function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const $btn = $(this);
                const defaultImg = $btn.attr("data-default");
                const targetId = $btn.attr("data-target");
                const index = parseInt($btn.attr("data-index"));
                
                console.log("Gallery reset clicked:", { defaultImg, targetId, index });
                
                if (confirm("Reset this image to default?")) {
                    // Update preview image
                    $("#" + targetId).attr("src", defaultImg);
                    
                    // Update hidden input
                    $btn.closest(".vcms-image-item").find('input[name="current_image_' + (index + 1) + '"]').val(defaultImg);
                    
                    // Add hidden input to mark reset
                    $('input[name="reset_image_' + (index + 1) + '"]').remove();
                    $btn.closest(".vcms-image-item").append('<input type="hidden" name="reset_image_' + (index + 1) + '" value="' + defaultImg + '">');
                    
                    // Clear file input
                    $btn.closest(".vcms-image-item").find('input[type="file"]').val('');
                    
                    // Visual feedback
                    $btn.html('<i class="fa-solid fa-check"></i>').css('background', '#4CAF50');
                    setTimeout(function() {
                        $btn.html('<i class="fa-solid fa-undo"></i>').css('background', '#2196F3');
                    }, 1500);
                    
                    console.log("Gallery reset completed for index:", index);
                }
            });

            // Drag and drop for file upload
            $(document).on("dragover", "#vcms-dropzone", function (e) {
                e.preventDefault();
                $(this).css("border-color", "#FFD700").css("background", "#fffef0");
            });

            $(document).on("dragleave", "#vcms-dropzone", function (e) {
                e.preventDefault();
                $(this).css("border-color", "#ddd").css("background", "transparent");
            });

            $(document).on("drop", "#vcms-dropzone", function (e) {
                e.preventDefault();
                $(this).css("border-color", "#ddd").css("background", "transparent");
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $("#vcms-file-input-main")[0].files = files;
                    self.handleMainFilePreview($("#vcms-file-input-main")[0]);
                }
            });
        },

        // =======================================
        // FILE PREVIEW HANDLERS
        // =======================================
        handleFilePreview: function (input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const previewId = $(input).data("preview");
                
                reader.onload = function (e) {
                    if (previewId) {
                        $("#" + previewId).attr("src", e.target.result);
                        $("#" + previewId + "-container").show();
                    }
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        },

        handleMainFilePreview: function (input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    $("#vcms-new-preview-img").attr("src", e.target.result);
                    $("#vcms-new-image-preview").show();
                };

                reader.readAsDataURL(input.files[0]);
            }
        },

        // Convert YouTube URL to embed URL (nocookie for privacy)
        convertYouTubeUrl: function(url) {
            if (!url) return url;
            
            // Already an embed URL
            if (url.includes('youtube.com/embed/') || url.includes('youtube-nocookie.com/embed/')) {
                // Convert to nocookie if not already
                return url.replace('youtube.com/embed/', 'youtube-nocookie.com/embed/');
            }
            
            let videoId = null;
            
            // youtu.be/VIDEO_ID
            if (url.includes('youtu.be/')) {
                const match = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
                if (match) videoId = match[1];
            }
            // youtube.com/watch?v=VIDEO_ID
            else if (url.includes('youtube.com/watch')) {
                const match = url.match(/[?&]v=([a-zA-Z0-9_-]+)/);
                if (match) videoId = match[1];
            }
            // youtube.com/v/VIDEO_ID
            else if (url.includes('youtube.com/v/')) {
                const match = url.match(/youtube\.com\/v\/([a-zA-Z0-9_-]+)/);
                if (match) videoId = match[1];
            }
            
            if (videoId) {
                return 'https://www.youtube-nocookie.com/embed/' + videoId;
            }
            
            return url;
        },

        handleDeleteImage: function (index, element) {
            const self = this;
            
            if (confirm("Are you sure you want to mark this image for deletion? It will be removed when you save.")) {
                element.css("opacity", "0.5").find(".vcms-image-preview").css("background", "#ffebee");
                element.find(".vcms-btn-delete-image").replaceWith(
                    '<span style="color: #ff4444; font-size: 0.8em;"><i class="fa-solid fa-trash"></i> Marked for deletion</span>'
                );
                // Add hidden input to mark for deletion
                element.append('<input type="hidden" name="delete_image_' + index + '" value="1">');
            }
        },

        // Reset image to default
        resetToDefault: function (targetId, defaultImg, inputName) {
            if (confirm("Reset this image to default?")) {
                // Build full URL for preview - check if already has protocol
                let fullUrl = defaultImg;
                if (!defaultImg.startsWith('http') && !defaultImg.startsWith('/')) {
                    fullUrl = '/' + defaultImg;
                }
                
                // Update preview image
                const $img = $("#" + targetId);
                $img.attr("src", fullUrl);
                
                // Remove any existing reset hidden input for this field
                $('input[name="reset_' + inputName + '"]').remove();
                
                // Add hidden input to indicate reset to default
                $(".vcms-edit-modal-body").append(
                    '<input type="hidden" name="reset_' + inputName + '" value="' + defaultImg + '">'
                );
                
                // Clear any file input for this field
                $('input[name="' + inputName + '"]').val('');
                $('#main-preview-container').hide();
                $('#circle-preview-container').hide();
                $('#vcms-new-image-preview').hide();
                
                // Show notification
                this.showNotification("Image will be reset to default when saved", "success");
                
                console.log("Reset to default:", inputName, "->", defaultImg);
            }
        },

        // Reset gallery image to default
        resetGalleryImage: function (index, defaultImg) {
            if (confirm("Reset this image to default?")) {
                // Build full URL for preview
                let fullUrl = defaultImg;
                if (!defaultImg.startsWith('http') && !defaultImg.startsWith('/')) {
                    fullUrl = '/' + defaultImg;
                }
                
                // Update preview image
                $("#gallery-img-" + index).attr("src", fullUrl);
                
                // Update hidden input
                $('input[name="current_image_' + (index + 1) + '"]').val(defaultImg);
                
                // Remove any existing reset hidden input
                $('input[name="reset_image_' + (index + 1) + '"]').remove();
                
                // Add hidden input to mark reset
                $(".vcms-edit-modal-body").append(
                    '<input type="hidden" name="reset_image_' + (index + 1) + '" value="' + defaultImg + '">'
                );
                
                // Clear file input
                $('input[name="image_' + (index + 1) + '"]').val('');
                
                // Show notification
                this.showNotification("Image " + (index + 1) + " will be reset to default when saved", "success");
                
                console.log("Gallery reset to default, index:", index, "->", defaultImg);
            }
        },

        // Service counter for new services
        newServiceCounter: 100,

        // Add new service
        addNewService: function () {
            const currentCount = $("#services-list-container .vcms-service-item").length;
            
            // Generate new unique ID using timestamp + random string to avoid collision
            const newId = "new_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
            const newIndex = currentCount;
            
            const defaultImg = "images/service-image-1.jpg";
            const newServiceHTML = `
                <div class="vcms-service-item" data-service-id="${newId}" data-index="${newIndex}" style="background: #252525; border: 2px solid #ffcc01; padding: 20px; border-radius: 12px; margin-bottom: 15px; position: relative; box-shadow: 0 0 20px rgba(255, 204, 1, 0.15);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #3a3a3a; margin-bottom: 15px; padding-bottom: 12px;">
                        <span style="font-weight: 700; color: #ffcc01; font-size: 14px;">
                            <i class="fa-solid fa-plus-circle" style="margin-right: 10px;"></i>
                            New Service #${newIndex + 1}
                        </span>
                        <button type="button" class="btn btn-sm vcms-delete-service-btn" data-service-id="${newId}" data-index="${newIndex}" onclick="VCMSAdmin.deleteService('${newId}', ${newIndex}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 12px; border-radius: 6px; font-size: 12px; position: relative; z-index: 9999;">
                            <i class="fa-solid fa-trash" style="pointer-events: none;"></i> <span style="pointer-events: none;">Delete</span>
                        </button>
                    </div>

                    <div class="vcms-form-group">
                        <label>Service Title</label>
                        <input type="text" class="vcms-input" name="service_${newId}_title" value="" placeholder="Enter service title">
                    </div>

                    <div class="vcms-form-group">
                        <label>Description</label>
                        <textarea class="vcms-textarea" name="service_${newId}_desc" rows="2" placeholder="Enter service description"></textarea>
                    </div>

                    <div class="vcms-form-group">
                        <label>Background Image</label>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 120px; height: 80px; border-radius: 8px; overflow: hidden; border: 2px solid #3a3a3a; flex-shrink: 0;">
                                <img id="service-bg-${newId}" src="/${defaultImg}" style="width: 100%; height: 100%; object-fit: cover;" alt="Service Background">
                            </div>
                            <div style="flex: 1;">
                                <input type="file" class="vcms-service-bg-file" name="service_${newId}_image_file" data-service-id="${newId}" accept="image/*" style="display: none;" id="service-bg-file-${newId}">
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <button type="button" onclick="document.getElementById('service-bg-file-${newId}').click()" style="background: #3a3a3a; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-upload"></i> Choose Image
                                    </button>
                                    <button type="button" onclick="VCMSAdmin.resetServiceImage('${newId}', '${defaultImg}')" style="background: rgba(255, 152, 0, 0.2); color: #ff9800; border: 1px solid #ff9800; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                        <i class="fa-solid fa-undo"></i> Reset
                                    </button>
                                </div>
                                <p style="margin: 8px 0 0 0; font-size: 11px; color: #888;">Recommended: 800x600px</p>
                            </div>
                        </div>
                        <input type="hidden" name="service_${newId}_image" id="service-bg-value-${newId}" value="${defaultImg}">
                    </div>

                    <div class="vcms-form-group">
                        <label>Read More Link</label>
                        <input type="text" class="vcms-input" name="service_${newId}_link" value="/services" placeholder="Enter link URL">
                    </div>
                    
                    <input type="hidden" name="service_${newId}_new" value="1">
                </div>
            `;
            
            $("#services-list-container").append(newServiceHTML);
            $("#services-count").text(currentCount + 1);
            
            // Scroll to new service
            const container = $("#services-list-container");
            container.scrollTop(container[0].scrollHeight);
            
            this.showNotification("New service added. Fill in the details and save.", "success");
        },

        // Delete service
        deleteService: function (serviceId, index) {
            if (confirm("Are you sure you want to delete this service?")) {
                // Find item by data attribute (works with any ID format)
                const $item = $(`.vcms-service-item`).filter(function() {
                    return $(this).data('service-id') == serviceId;
                });
                
                // Check if this is a brand new service that hasn't been saved yet
                // A new unsaved service has input[name="service_${serviceId}_new"] with value="1"
                const isNewUnsaved = $(`input[name="service_${serviceId}_new"]`).val() === "1";
                
                // If NOT a new unsaved service, it exists in database and needs delete marker
                if (!isNewUnsaved) {
                    $(".vcms-edit-modal-body").append(
                        `<input type="hidden" name="delete_service_${serviceId}" value="1">`
                    );
                }
                
                // Remove from DOM with animation
                $item.css({
                    "background": "#ffebee",
                    "border-color": "#f44336",
                    "opacity": "0.5"
                });
                
                setTimeout(() => {
                    $item.slideUp(300, function() {
                        $(this).remove();
                        // Update count
                        const newCount = $("#services-list-container .vcms-service-item").length;
                        $("#services-count").text(newCount);
                    });
                }, 300);
                
                this.showNotification("Service marked for deletion", "success");
            }
        },

        // Reset service background image to default
        resetServiceImage: function (serviceId, defaultImg) {
            if (confirm("Reset this service background image to default?")) {
                // Build full URL for preview
                let fullUrl = defaultImg;
                if (!defaultImg.startsWith('http') && !defaultImg.startsWith('/')) {
                    fullUrl = '/' + defaultImg;
                }
                
                // Update preview image
                $("#service-bg-" + serviceId).attr("src", fullUrl);
                
                // Update hidden input value
                $("#service-bg-value-" + serviceId).val(defaultImg);
                
                // Clear file input
                $("#service-bg-file-" + serviceId).val("");
                
                // Add reset marker hidden input for server to know this was reset
                const resetInputName = "reset_service_" + serviceId + "_image";
                if ($('input[name="' + resetInputName + '"]').length === 0) {
                    $(".vcms-edit-modal-body").append(
                        '<input type="hidden" name="' + resetInputName + '" value="' + defaultImg + '">'
                    );
                }
                
                this.showNotification("Image reset to default", "success");
            }
        },

        // Reset team member image to default
        resetTeamImage: function (index, defaultImg) {
            if (confirm("Reset this team member image to default?")) {
                // Build full URL for preview
                let fullUrl = defaultImg;
                if (!defaultImg.startsWith('http') && !defaultImg.startsWith('/')) {
                    fullUrl = '/' + defaultImg;
                }
                
                // Update preview image
                $("#team-img-" + index).attr("src", fullUrl);
                
                // Update hidden input
                $('input[name="current_member_' + index + '_img"]').val(defaultImg);
                
                // Remove any existing reset hidden input
                $('input[name="reset_member_' + index + '_img"]').remove();
                
                // Add hidden input to mark reset
                $(".vcms-edit-modal-body").append(
                    '<input type="hidden" name="reset_member_' + index + '_img" value="' + defaultImg + '">'
                );
                
                // Clear file input
                $('input[name="member_' + index + '_img"]').val('');
                
                // Show notification
                this.showNotification("Team member image will be reset to default when saved", "success");
                
                console.log("Team image reset to default, index:", index, "->", defaultImg);
            }
        },

        // Add new team member (full form)
        addTeamMember: function () {
            const currentCount = $("#team-list-container .vcms-team-item").length;
            const newIndex = currentCount;
            const defaultImg = "images/team-" + ((newIndex % 8) + 1) + ".jpg";
            
            const newMemberHTML = `
                <div class="vcms-team-item" data-index="${newIndex}" style="background: #252525; border: 2px solid #4caf50; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 0 20px rgba(76, 175, 80, 0.15);">
                    
                    <!-- Header -->
                    <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; border-bottom: 2px solid #4caf50; padding-bottom: 15px; justify-content: space-between;">
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <img id="team-img-${newIndex}" src="/${defaultImg}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid #4caf50;">
                            <div>
                                <div style="font-weight: 700; color: #4caf50; font-size: 18px;"><i class="fa-solid fa-plus-circle" style="margin-right: 8px;"></i>New Member #${newIndex + 1}</div>
                                <div style="color: #e0e0e0; font-size: 14px;">Fill in the details below</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteTeamMember(${newIndex}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px; position: relative; z-index: 9999;">
                            <i class="fa-solid fa-trash" style="pointer-events: none;"></i> Delete
                        </button>
                    </div>

                    <!-- Two Column Layout -->
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 25px; margin-bottom: 20px;">
                        
                        <!-- Left: Photo -->
                        <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; text-align: center;">
                            <img id="team-img-preview-${newIndex}" src="/${defaultImg}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 2px solid #3a3a3a; margin-bottom: 10px;">
                            <input type="hidden" name="current_member_${newIndex}_img" value="${defaultImg}">
                            <input type="hidden" name="default_member_${newIndex}_img" value="${defaultImg}">
                            <input type="file" class="vcms-input" name="member_${newIndex}_img" accept="image/*" style="font-size: 12px;">
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.resetTeamImage(${newIndex}, '${defaultImg}')" style="margin-top: 8px; background: rgba(255, 152, 0, 0.2); color: #ff9800; border: 1px solid #ff9800; padding: 6px 12px; border-radius: 6px; width: 100%;">
                                <i class="fa-solid fa-undo"></i> Reset
                            </button>
                        </div>
                        
                        <!-- Right: Basic Info -->
                        <div>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <div class="vcms-form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="vcms-input" name="member_${newIndex}_name" value="" placeholder="Enter name">
                                </div>
                                <div class="vcms-form-group">
                                    <label>Position / Role</label>
                                    <input type="text" class="vcms-input" name="member_${newIndex}_role" value="" placeholder="Enter role">
                                </div>
                                <div class="vcms-form-group">
                                    <label>Experience</label>
                                    <input type="text" class="vcms-input" name="member_${newIndex}_experience" value="" placeholder="e.g., 5 years">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Me -->
                    <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; margin-bottom: 20px;">
                        <label style="font-weight: 600; display: block; margin-bottom: 12px; color: #ffcc01;"><i class="fa-solid fa-user"></i> About Me</label>
                        <textarea class="vcms-textarea" name="member_${newIndex}_about" rows="3" placeholder="Write a brief description about this team member..."></textarea>
                    </div>

                    <!-- Two Column: Skills & Expertise -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        
                        <!-- My Skills -->
                        <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <label style="font-weight: 600; color: #ffcc01; margin: 0;"><i class="fa-solid fa-chart-bar"></i> My Skills</label>
                                <button type="button" onclick="VCMSAdmin.addSkill(${newIndex})" style="background: #4caf50; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="vcms-form-group">
                                <label style="font-size: 12px;">Skills Description</label>
                                <textarea class="vcms-textarea" name="member_${newIndex}_skills_desc" rows="2" placeholder="Brief description of skills..."></textarea>
                            </div>
                            <div id="skills-list-${newIndex}" style="margin-top: 10px;">
                                <input type="hidden" name="member_${newIndex}_skills_count" id="skills-count-${newIndex}" value="1">
                                <div class="skill-item" data-index="1" style="display: flex; gap: 8px; margin-bottom: 0; align-items: center;">
                                    <input type="text" class="vcms-input" name="member_${newIndex}_skill_1_title" value="" placeholder="Skill name" style="flex: 2; margin: 0;">
                                    <input type="number" class="vcms-input vcms-percent-input" name="member_${newIndex}_skill_1_percent" value="80" min="0" max="100" placeholder="%" style="flex: 1; margin: 0;" onchange="VCMSAdmin.clampPercent(this)">
                                    <span style="color: #888;">%</span>
                                    <button type="button" onclick="VCMSAdmin.deleteSkill(${newIndex}, 1)" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- My Expertise -->
                        <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <label style="font-weight: 600; color: #ffcc01; margin: 0;"><i class="fa-solid fa-star"></i> My Expertise</label>
                                <button type="button" onclick="VCMSAdmin.addExpertise(${newIndex})" style="background: #4caf50; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="vcms-form-group">
                                <label style="font-size: 12px;">Expertise Description</label>
                                <textarea class="vcms-textarea" name="member_${newIndex}_expertise_desc" rows="2" placeholder="Brief description of expertise..."></textarea>
                            </div>
                            <div id="expertise-list-${newIndex}" style="margin-top: 10px;">
                                <input type="hidden" name="member_${newIndex}_expertise_count" id="expertise-count-${newIndex}" value="2">
                                <div class="expertise-item" data-index="1" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                    <input type="text" class="vcms-input" name="member_${newIndex}_expertise_1" value="" placeholder="Expertise 1" style="flex: 1; margin: 0;">
                                    <button type="button" onclick="VCMSAdmin.deleteExpertise(${newIndex}, 1)" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                                </div>
                                <div class="expertise-item" data-index="2" style="display: flex; gap: 8px; margin-bottom: 0;">
                                    <input type="text" class="vcms-input" name="member_${newIndex}_expertise_2" value="" placeholder="Expertise 2" style="flex: 1; margin: 0;">
                                    <button type="button" onclick="VCMSAdmin.deleteExpertise(${newIndex}, 2)" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="member_${newIndex}_new" value="1">
                </div>
            `;
            
            $("#team-list-container").append(newMemberHTML);
            $("#team-count").text(currentCount + 1);
            $("#team-member-count").val(currentCount + 1);
            
            // Scroll to new member
            const container = $("#team-list-container");
            container.animate({ scrollTop: container[0].scrollHeight }, 300);
            
            this.showNotification("New team member added. Fill in the details and save.", "success");
        },

        // Delete team member
        deleteTeamMember: function (index) {
            if (confirm("Are you sure you want to delete this team member?")) {
                const $item = $(".vcms-team-item[data-index='" + index + "']");
                
                // Check if member exists in database (doesn't have _new hidden input)
                const isNew = $('input[name="member_' + index + '_new"]').length > 0;
                
                if (!isNew) {
                    // Add hidden input to mark for deletion (like service delete pattern)
                    $(".vcms-edit-modal-body").append(
                        '<input type="hidden" name="delete_member_' + index + '" value="' + index + '">'
                    );
                }
                
                // Remove from DOM with animation
                $item.css({
                    "background": "#ffebee",
                    "border-color": "#f44336",
                    "opacity": "0.5"
                });
                
                setTimeout(function() {
                    $item.slideUp(300, function() {
                        $(this).remove();
                        
                        // REINDEX semua remaining items untuk sinkronisasi dengan backend
                        VCMSAdmin.reindexTeamMembers();
                        
                        // Update count
                        const newCount = $("#team-list-container .vcms-team-item").length;
                        $("#team-count").text(newCount);
                        $("#team-member-count").val(newCount);
                    });
                }, 200);
                
                this.showNotification("Team member will be deleted when saved", "success");
            }
        },
        
        // Add expertise item to team member (max 5)
        addExpertise: function(memberIndex) {
            const container = $(`#expertise-list-${memberIndex}`);
            const currentCount = container.find('.expertise-item').length;
            
            // Limit to 5 items
            if (currentCount >= 5) {
                this.showNotification("Maximum 5 expertise items allowed", "error");
                return;
            }
            
            const newIndex = currentCount + 1;
            
            const newItemHTML = `
                <div class="expertise-item" data-index="${newIndex}" style="display: flex; gap: 8px; margin-bottom: 8px;">
                    <input type="text" class="vcms-input" name="member_${memberIndex}_expertise_${newIndex}" value="" placeholder="Expertise ${newIndex}" style="flex: 1; margin: 0;">
                    <button type="button" onclick="VCMSAdmin.deleteExpertise(${memberIndex}, ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                </div>
            `;
            
            container.append(newItemHTML);
            $(`#expertise-count-${memberIndex}`).val(newIndex);
            
            // Focus on new input
            container.find('.expertise-item:last input').focus();
            
            this.showNotification("Expertise item added", "success");
        },
        
        // Add skill item to team member (max 5)
        addSkill: function(memberIndex) {
            console.log("addSkill called for member:", memberIndex);
            
            // Cari container dalam modal terlebih dahulu
            const $modal = $('.vcms-edit-modal-body');
            let container = $modal.find(`#skills-list-${memberIndex}`);
            
            // Fallback ke selector global jika tidak ditemukan di modal
            if (!container.length) {
                container = $(`#skills-list-${memberIndex}`);
            }
            
            console.log("Skills container found:", container.length, "Modal exists:", $modal.length);
            
            if (!container.length) {
                console.error(`Skills container not found: #skills-list-${memberIndex}`);
                this.showNotification("Error: Skills container not found", "error");
                return;
            }
            
            const currentCount = container.find('.skill-item').length;
            console.log("Current skill count:", currentCount);
            
            if (currentCount >= 5) {
                this.showNotification("Maximum 5 skill items allowed", "error");
                return;
            }
            
            const newIndex = currentCount + 1;
            const mIdx = memberIndex;
            
            const newItemHTML = `
                <div class="skill-item" data-index="${newIndex}" style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                    <input type="text" class="vcms-input" name="member_${mIdx}_skill_${newIndex}_title" value="" placeholder="Skill name" style="flex: 2; margin: 0;">
                    <input type="number" class="vcms-input vcms-percent-input" name="member_${mIdx}_skill_${newIndex}_percent" value="80" min="0" max="100" placeholder="%" style="flex: 1; margin: 0;" onchange="VCMSAdmin.clampPercent(this)">
                    <span style="color: #888;">%</span>
                    <button type="button" onclick="VCMSAdmin.deleteSkill(${mIdx}, ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                </div>
            `;
            
            container.append(newItemHTML);
            
            // Update count - cari juga dalam modal
            let countInput = $modal.find(`#skills-count-${mIdx}`);
            if (!countInput.length) countInput = $(`#skills-count-${mIdx}`);
            countInput.val(newIndex);
            
            container.find('.skill-item:last input[type="text"]').focus();
            
            this.showNotification("Skill item added", "success");
        },

        // Delete skill item from team member
        deleteSkill: function(memberIndex, skillIndex) {
            console.log("deleteSkill called:", memberIndex, skillIndex);
            
            // Cari container dalam modal terlebih dahulu
            const $modal = $('.vcms-edit-modal-body');
            let container = $modal.find(`#skills-list-${memberIndex}`);
            
            // Fallback ke selector global
            if (!container.length) {
                container = $(`#skills-list-${memberIndex}`);
            }
            
            console.log("Skills container found:", container.length);
            
            if (!container.length) {
                console.error(`Skills container not found: #skills-list-${memberIndex}`);
                return;
            }
            
            const itemCount = container.find('.skill-item').length;
            
            if (itemCount <= 1) {
                this.showNotification("At least one skill item is required", "error");
                return;
            }
            
            // Remove the specific item
            const itemToRemove = container.find(`.skill-item[data-index="${skillIndex}"]`);
            console.log("Item to remove:", itemToRemove.length);
            itemToRemove.remove();
            
            // Reindex remaining items with stored memberIndex
            const mIdx = memberIndex;
            container.find('.skill-item').each(function(idx) {
                const newIdx = idx + 1;
                $(this).attr('data-index', newIdx);
                
                // Update input names
                $(this).find('input[type="text"]').attr('name', `member_${mIdx}_skill_${newIdx}_title`);
                $(this).find('input[type="number"]').attr('name', `member_${mIdx}_skill_${newIdx}_percent`);
                
                // Update delete button
                $(this).find('button').attr('onclick', `VCMSAdmin.deleteSkill(${mIdx}, ${newIdx})`);
            });
            
            // Update count - cari juga dalam modal
            const newCount = container.find('.skill-item').length;
            let countInput = $modal.find(`#skills-count-${mIdx}`);
            if (!countInput.length) countInput = $(`#skills-count-${mIdx}`);
            countInput.val(newCount);
            console.log("New skill count:", newCount);
            
            this.showNotification("Skill item removed", "success");
        },
        
        // Clamp percentage value to 0-100
        clampPercent: function(input) {
            let value = parseInt($(input).val()) || 0;
            if (value > 100) value = 100;
            if (value < 0) value = 0;
            $(input).val(value);
        },

        // Delete expertise item from team member
        deleteExpertise: function(memberIndex, expertiseIndex) {
            const container = $(`#expertise-list-${memberIndex}`);
            const itemCount = container.find('.expertise-item').length;
            
            if (itemCount <= 1) {
                this.showNotification("At least one expertise item is required", "error");
                return;
            }
            
            // Remove the item
            container.find(`.expertise-item[data-index="${expertiseIndex}"]`).remove();
            
            // Reindex remaining items
            container.find('.expertise-item').each(function(idx) {
                const newIdx = idx + 1;
                $(this).attr('data-index', newIdx);
                
                // Update input name and placeholder
                const input = $(this).find('input[type="text"]');
                input.attr('name', `member_${memberIndex}_expertise_${newIdx}`);
                input.attr('placeholder', `Expertise ${newIdx}`);
                
                // Update delete button onclick
                $(this).find('button').attr('onclick', `VCMSAdmin.deleteExpertise(${memberIndex}, ${newIdx})`);
            });
            
            // Update count
            const newCount = container.find('.expertise-item').length;
            $(`#expertise-count-${memberIndex}`).val(newCount);
            
            this.showNotification("Expertise item removed", "success");
        },

        // Reindex team members after delete to sync with backend reindexing
        reindexTeamMembers: function() {
            $("#team-list-container .vcms-team-item").each(function(newIndex) {
                const $item = $(this);
                const oldIndex = parseInt($item.attr('data-index'));
                
                if (oldIndex !== newIndex) {
                    // Update data-index attribute
                    $item.attr('data-index', newIndex);
                    
                    // Update all input names
                    $item.find('input, textarea, select').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(new RegExp('member_' + oldIndex + '_', 'g'), 'member_' + newIndex + '_')
                                                .replace(new RegExp('current_member_' + oldIndex + '_', 'g'), 'current_member_' + newIndex + '_')
                                                .replace(new RegExp('default_member_' + oldIndex + '_', 'g'), 'default_member_' + newIndex + '_');
                            $(this).attr('name', newName);
                        }
                    });
                    
                    // Update image preview ID
                    $item.find('img[id="team-img-' + oldIndex + '"]').attr('id', 'team-img-' + newIndex);
                    
                    // Update delete button onclick
                    $item.find('button[onclick*="deleteTeamMember"]').attr(
                        'onclick', 
                        'VCMSAdmin.deleteTeamMember(' + newIndex + '); return false;'
                    );
                    
                    // Update reset button onclick
                    $item.find('button[onclick*="resetTeamImage"]').each(function() {
                        const onclick = $(this).attr('onclick');
                        if (onclick) {
                            const newOnclick = onclick.replace(/resetTeamImage\(\d+,/, 'resetTeamImage(' + newIndex + ',');
                            $(this).attr('onclick', newOnclick);
                        }
                    });
                }
            });
            
            console.log("Team members reindexed successfully");
        },

        // =======================================
        // SIDEBAR TOGGLE - Fullscreen Mode (Desktop) / Sidebar Toggle (Mobile)
        // =======================================
        initSidebar: function () {
            const $wrapper = $(".admin-wrapper");
            const $sidebar = $("#adminSidebar");
            const $overlay = $("#sidebarOverlay");
            const $sidebarBurger = $("#sidebarBurger");
            const $topbarBurger = $("#sidebarBurgerTopbar");
            
            const isMobile = () => window.innerWidth <= 1024;
            
            const openMobileSidebar = () => {
                $sidebar.addClass("active");
                $overlay.addClass("active");
                $("body").css("overflow", "hidden");
            };
            
            const closeMobileSidebar = () => {
                $sidebar.removeClass("active");
                $overlay.removeClass("active");
                $("body").css("overflow", "");
            };
            
            // Sidebar burger (Desktop only) - go fullscreen
            $sidebarBurger.on("click", function () {
                if (!isMobile()) {
                    $wrapper.addClass("fullscreen");
                }
            });
            
            // Topbar burger - different behavior for desktop vs mobile
            $topbarBurger.on("click", function () {
                if (isMobile()) {
                    // Mobile: toggle sidebar visibility
                    if ($sidebar.hasClass("active")) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    // Desktop: exit fullscreen mode
                    $wrapper.removeClass("fullscreen");
                }
            });
            
            // Close sidebar when clicking overlay on mobile
            $overlay.on("click", function () {
                closeMobileSidebar();
            });
            
            // Close sidebar on window resize if switching to desktop
            $(window).on("resize", function () {
                if (!isMobile() && $sidebar.hasClass("active")) {
                    closeMobileSidebar();
                }
            });
        },

        // =======================================
        // OPEN EDIT MODAL
        // =======================================
        openEditModal: function (editType, element) {
            console.log("openEditModal called:", editType, element);
            
            // Ensure modal is initialized
            if (!this.modal || this.modal.length === 0) {
                this.modal = $("#vcmsEditModal");
                console.log("Modal re-initialized:", this.modal.length);
            }
            
            this.currentEditItem = element;
            const modalBody = $(".vcms-edit-modal-body");
            const modalContent = $(".vcms-edit-modal-content");

            // Reset saving state when opening modal
            this.isSaving = false;
            $(".btn-save-modal")
                .prop("disabled", false)
                .removeClass("saving")
                .html("Save");

            // Clear previous content
            modalBody.html("");
            
            // Reset modal size to default
            modalContent.css({
                'max-width': '',
                'width': '',
                'max-height': ''
            });
            
            // Make modal larger for team-list edit
            if (editType === 'team-list') {
                modalContent.css({
                    'max-width': '95vw',
                    'width': '95vw',
                    'max-height': '95vh'
                });
            }

            // Generate form based on edit type
            let formHTML = "";
            try {
                formHTML = this.generateEditForm(editType, element);
                console.log("Form generated successfully, length:", formHTML.length);
            } catch (error) {
                console.error("Error generating form:", error);
                formHTML = `<div style="padding: 20px; color: #f44336;">Error generating form: ${error.message}</div>`;
            }
            modalBody.html(formHTML);

            // Initialize tab switching for vision-mission and similar forms
            // Unbind previous event listeners to prevent duplication
            modalBody.find(".vcms-tab-btn").off("click");
            modalBody.find(".vcms-tab-btn").on("click", function() {
                const tabId = $(this).data("tab");
                
                // Update button styles
                modalBody.find(".vcms-tab-btn").css({
                    "background": "#333",
                    "color": "#fff"
                }).removeClass("active");
                $(this).css({
                    "background": "#ffcc01",
                    "color": "#161616"
                }).addClass("active");
                
                // Show/hide tab content
                modalBody.find(".vcms-tab-content").hide().removeClass("active");
                modalBody.find(`.vcms-tab-content[data-tab-content="${tabId}"]`).show().addClass("active");
            });
            
            // Initialize file preview for image inputs with data-preview attribute
            // Unbind previous event listeners to prevent duplication
            modalBody.find("input[type='file'][data-preview]").off("change");
            modalBody.find("input[type='file'][data-preview]").on("change", function() {
                const previewId = $(this).data("preview");
                const file = this.files[0];
                if (file && previewId) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#${previewId}`).attr("src", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Show modal
            console.log("Showing modal, modal element:", this.modal, "modal length:", this.modal.length);
            this.modal.addClass("active");
            $("body").css("overflow", "hidden");

            console.log("Modal shown, has active class:", this.modal.hasClass("active"));
        },

        // =======================================
        // CLOSE EDIT MODAL
        // =======================================
        closeEditModal: function () {
            this.modal.removeClass("active");
            $("body").css("overflow", "auto");
            this.currentEditItem = null;
            // Reset saving flag when modal closed (unless reload is pending)
            if (!this.isSaving) {
                $(".btn-save-modal").html("Save").prop("disabled", false);
            }
        },

        // =======================================
        // GENERATE EDIT FORM
        // =======================================
        generateEditForm: function (editType, element) {
            let formHTML = "";

            switch (editType) {
                case "hero":
                case "hero-content":
                    formHTML = this.generateHeroForm(element);
                    break;

                case "text":
                case "section-title":
                    formHTML = this.generateTextForm(element);
                    break;

                case "video":
                    formHTML = this.generateVideoForm(element);
                    break;

                case "hero-images":
                case "client-logos":
                    formHTML = this.generateImageGalleryForm(element);
                    break;

                case "portfolio-item":
                    formHTML = this.generatePortfolioItemForm(element);
                    break;

                case "services-tabs":
                    formHTML = this.generateServicesForm(element);
                    break;
                case "trusted-clients":
                    formHTML = this.generateTrustedClientsForm(element);
                    break;
                case "services":
                    formHTML = this.generateServicesSectionForm(element);
                    break;
                case "service-list":
                    formHTML = this.generateServiceListForm(element);
                    break;
                case "service-detail":
                    formHTML = this.generateServiceDetailForm(element);
                    break;
                case "our-work":
                    formHTML = this.generateWorkSectionForm(element);
                    break;
                case "project-list":
                    formHTML = this.generateProjectListForm(element);
                    break;
                case "creative-tools":
                    formHTML = this.generateCreativeToolsForm(element);
                    break;
                case "tools-list":
                    formHTML = this.generateToolsListForm(element);
                    break;
                case "how-it-works":
                    formHTML = this.generateHowItWorksForm(element);
                    break;
                case "process-list":
                    formHTML = this.generateProcessListForm(element);
                    break;
                case "image-link":
                    formHTML = this.generateImageWithLinkForm(element);
                    break;
                case "image-group":
                    formHTML = this.generateHowImagesForm(element);
                    break;
                case "achievements":
                case "achievements-title":
                    formHTML = this.generateAchievementsHeaderForm(element);
                    break;
                case "achievement-list":
                    formHTML = this.generateAchievementListForm(element);
                    break;
                case "rich-text":
                    formHTML = this.generateRichTextForm(element);
                    break;
                case "counter-list":
                    formHTML = this.generateCounterListForm(element);
                    break;
                case "cta":
                    formHTML = this.generateCtaSectionForm(element);
                    break;
                case "our-team":
                    formHTML = this.generateTeamSectionForm(element);
                    break;
                case "team-list":
                    formHTML = this.generateTeamListForm(element);
                    break;
                case "testimonials":
                    formHTML = this.generateTestimonialHeaderForm(element);
                    break;
                case "footer":
                case "footer-content":
                    formHTML = this.generateFooterForm(element);
                    break;
                case "client-logo-slider":
                    formHTML = this.generateClientLogosForm(element);
                    break;
                case "vision-mission":
                    formHTML = this.generateVisionMissionForm(element);
                    break;
                case "about-header":
                    formHTML = this.generateAboutHeaderForm(element);
                    break;
                case "page-header":
                    formHTML = this.generatePageHeaderForm(element);
                    break;
                case "about-section":
                    formHTML = this.generateAboutSectionForm(element);
                    break;
                case "pricing-header":
                    formHTML = this.generatePricingHeaderForm(element);
                    break;
                case "pricing-section":
                    formHTML = this.generatePricingSectionForm(element);
                    break;
                case "pricing-benefits":
                    formHTML = this.generatePricingBenefitsForm(element);
                    break;
                case "contact-header":
                    formHTML = this.generateContactHeaderForm(element);
                    break;
                case "contact-section":
                    formHTML = this.generateContactSectionForm(element);
                    break;
                case "contact-form":
                    formHTML = this.generateContactFormSettingsForm(element);
                    break;
                case "map-section":
                    formHTML = this.generateMapSectionForm(element);
                    break;
                default:
                    formHTML = this.generateGenericForm(element);
            }

            return formHTML;
        },

        // =======================================
        // FORM GENERATORS
        // =======================================
        generateHeroForm: function (element) {
            const title = element.find("h1").html() || "";
            const description = element.find("p").first().text() || "";

            return `
                <div class="vcms-form-group">
                    <label>Hero Title</label>
                    <input type="text" class="vcms-input" name="hero_title" value="${this.stripHTML(
                        title
                    )}">
                    <small>Use &lt;span&gt; tags for highlighted text</small>
                </div>
                <div class="vcms-form-group">
                    <label>Hero Description</label>
                    <textarea class="vcms-textarea" name="hero_description" rows="4">${this.escapeHTML(
                        description
                    )}</textarea>
                </div>
                <div class="vcms-form-group">
                    <label>Button Text</label>
                    <input type="text" class="vcms-input" name="btn_text" value="view our portfolio">
                </div>
                <div class="vcms-form-group">
                    <label>Button Link</label>
                    <input type="text" class="vcms-input" name="btn_link" value="/portfolio">
                </div>
            `;
        },

        generateTextForm: function (element) {
            const textContent = element.text() || "";

            return `
                <div class="vcms-form-group">
                    <label>Text Content</label>
                    <textarea class="vcms-textarea" name="text_content" rows="6">${this.escapeHTML(
                        textContent
                    )}</textarea>
                </div>
            `;
        },

        generateVideoForm: function (element) {
            const videoSrc = element.find("source").attr("src") || "";

            return `
                <div class="vcms-form-group">
                    <label>Video URL</label>
                    <input type="text" class="vcms-input" name="video_url" value="${this.escapeHTML(
                        videoSrc
                    )}">
                    <small>Supports: MP4 video URL or YouTube embed URL</small>
                </div>
                <div class="vcms-form-group">
                    <label>Video Type</label>
                    <select class="vcms-select" name="video_type">
                        <option value="mp4">Self-hosted MP4</option>
                        <option value="youtube">YouTube Video</option>
                    </select>
                </div>
            `;
        },

        generateImageGalleryForm: function (element) {
            const self = this;
            let imagesHTML = '';
            
            // Default gallery images (274x174)
            const defaultGalleryImages = [
                'images/hero-img-1.jpg',
                'images/hero-img-2.jpg',
                'images/hero-img-3.jpg',
                'images/hero-img-4.jpg',
                'images/hero-img-5.jpg'
            ];
            
            // Find all images in the gallery
            element.find('.hero-img img, .image-anime img').each(function(index) {
                const imgSrc = $(this).attr('src') || '';
                const imgAlt = $(this).attr('alt') || 'Image ' + (index + 1);
                const defaultImg = defaultGalleryImages[index] || defaultGalleryImages[0];
                
                imagesHTML += `
                    <div class="vcms-image-item" data-index="${index}">
                        <div class="vcms-image-preview">
                            <img id="gallery-img-${index}" src="${imgSrc}" alt="${imgAlt}">
                            <input type="hidden" name="current_image_${index + 1}" value="${imgSrc}">
                            <input type="hidden" name="default_image_${index + 1}" value="${defaultImg}">
                        </div>
                        <div class="vcms-image-input">
                            <label>Replace Image ${index + 1}</label>
                            <input type="file" class="vcms-input vcms-file-input" name="image_${index + 1}" accept="image/*" data-preview-target="${index}">
                            <button type="button" class="btn btn-sm btn-primary" onclick="VCMSAdmin.resetGalleryImage(${index}, '${defaultImg}')" style="margin-top: 5px; width: 100%;">
                                <i class="fa-solid fa-undo"></i> Reset to Default
                            </button>
                        </div>
                    </div>
                `;
            });

            return `
                <div class="vcms-form-group">
                    <label>Gallery Images (${element.find('.hero-img img, .image-anime img').length} images)</label>
                    <div class="vcms-image-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                        ${imagesHTML}
                    </div>
                    <small style="display: block; margin-top: 10px; color: #888;">Click "Reset to Default" button to restore original image.</small>
                </div>
                <style>
                    .vcms-image-item { background: #252525; border-radius: 12px; padding: 12px; border: 1px solid #3a3a3a; }
                    .vcms-image-preview { position: relative; border-radius: 8px; overflow: hidden; margin-bottom: 10px; border: 1px solid #3a3a3a; }
                    .vcms-image-preview img { width: 100%; height: 100px; object-fit: cover; display: block; }
                    .vcms-image-input label { font-size: 0.75em; color: #999; margin-bottom: 5px; display: block; }
                    .vcms-image-input input { font-size: 0.8em; background: #161616; color: #fff; border: 1px solid #3a3a3a; border-radius: 6px; padding: 8px; }
                    .vcms-image-input input:focus { border-color: #ffcc01; outline: none; }
                </style>
            `;
        },

        generatePortfolioItemForm: function (element) {
            const title = element.find("h3").text() || "";
            const description = element.find("p").first().text() || "";

            return `
                <div class="vcms-form-group">
                    <label>Portfolio Title</label>
                    <input type="text" class="vcms-input" name="portfolio_title" value="${this.escapeHTML(
                        title
                    )}">
                </div>
                <div class="vcms-form-group">
                    <label>Portfolio Description</label>
                    <textarea class="vcms-textarea" name="portfolio_description" rows="3">${this.escapeHTML(
                        description
                    )}</textarea>
                </div>
                <div class="vcms-form-group">
                    <label>Media Type</label>
                    <select class="vcms-select" name="media_type">
                        <option value="youtube">YouTube Video</option>
                        <option value="image">Image</option>
                        <option value="video">Self-hosted Video</option>
                    </select>
                </div>
                <div class="vcms-form-group">
                    <label>Media URL</label>
                    <input type="text" class="vcms-input" name="media_url" placeholder="YouTube URL or Image URL">
                </div>
            `;
        },

        generateServicesForm: function (element) {
            return `
                <div class="vcms-form-group">
                    <label>Services Section</label>
                    <p>Click "Manage Services" to add, edit or remove service items.</p>
                    <a href="/admin/services" class="btn-manage-services">
                        <i class="fa-solid fa-cog"></i> Manage Services
                    </a>
                </div>
            `;
        },

        // Generator untuk About Header Section
        generateAboutHeaderForm: function (element) {
            const self = this;
            const title = element.find(".page-header-box h1").html() || "About our <span>journey</span>";
            
            return `
                <div class="vcms-form-group">
                    <label><i class="fa-solid fa-heading"></i> Page Title</label>
                    <input type="text" class="vcms-input" name="title" value="${self.stripHTML(title)}">
                    <small style="color: #888;">Use &lt;span&gt; for highlighted text. Example: About our &lt;span&gt;journey&lt;/span&gt;</small>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: rgba(255, 204, 1, 0.1); border-radius: 8px; border-left: 4px solid #ffcc01;">
                    <p style="margin: 0; color: #e0e0e0; font-size: 13px;">
                        <i class="fa-solid fa-info-circle" style="color: #ffcc01; margin-right: 8px;"></i>
                        Breadcrumb navigation is automatically generated based on the page structure.
                    </p>
                </div>
            `;
        },

        // Generator untuk Page Header Section (Generic - untuk Services, Contact, dll)
        generatePageHeaderForm: function (element) {
            const self = this;
            const $item = $(element).find('.vcms-editable-item[data-edit-type="page-header"]');
            
            // Get data from attributes using attr() to avoid jQuery camelCase conversion
            const title = $item.attr("data-title") || element.find(".page-header-box h1").html() || "Page Title";
            const breadcrumb = $item.attr("data-breadcrumb") || "Page";
            
            return `
                <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                    <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                        <i class="fa-solid fa-heading"></i> Page Header Settings
                    </h4>
                    
                    <div class="vcms-form-group">
                        <label><i class="fa-solid fa-quote-left"></i> Page Title</label>
                        <input type="text" class="vcms-input" name="title" value="${self.escapeHTML(title)}">
                        <small style="color: #888; display: block; margin-top: 8px;">
                            Use &lt;span&gt; for highlighted text. Example: Explore our &lt;span&gt;services&lt;/span&gt;
                        </small>
                    </div>
                    
                    <div class="vcms-form-group" style="margin-bottom: 0;">
                        <label><i class="fa-solid fa-compass"></i> Breadcrumb Text</label>
                        <input type="text" class="vcms-input" name="breadcrumb" value="${self.escapeHTML(breadcrumb)}">
                        <small style="color: #888; display: block; margin-top: 8px;">
                            This text will appear in the breadcrumb navigation (e.g., "Services", "Contact", etc.)
                        </small>
                    </div>
                </div>
                
                <div style="padding: 15px; background: rgba(255, 204, 1, 0.1); border-radius: 8px; border-left: 4px solid #ffcc01;">
                    <p style="margin: 0; color: #e0e0e0; font-size: 13px;">
                        <i class="fa-solid fa-info-circle" style="color: #ffcc01; margin-right: 8px;"></i>
                        The "home" breadcrumb link is automatically added before your custom breadcrumb text.
                    </p>
                </div>
            `;
        },

        // Generator untuk Pricing Header Section
        generatePricingHeaderForm: function (element) {
            const self = this;
            const title = element.find(".page-header-box h1").html() || "Pricing <span>plan</span>";
            
            return `
                <div class="vcms-form-group">
                    <label><i class="fa-solid fa-heading"></i> Page Title</label>
                    <input type="text" class="vcms-input" name="title" value="${self.stripHTML(title)}">
                    <small style="color: #888;">Use &lt;span&gt; for highlighted text. Example: Pricing &lt;span&gt;plan&lt;/span&gt;</small>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: rgba(255, 204, 1, 0.1); border-radius: 8px; border-left: 4px solid #ffcc01;">
                    <p style="margin: 0; color: #e0e0e0; font-size: 13px;">
                        <i class="fa-solid fa-info-circle" style="color: #ffcc01; margin-right: 8px;"></i>
                        Breadcrumb navigation is automatically generated based on the page structure.
                    </p>
                </div>
            `;
        },

        // Generator untuk Pricing Section Form
        generatePricingSectionForm: function (element) {
            const self = this;
            const plans = [];
            
            // Get all pricing boxes
            element.find(".pricing-box").each(function (index) {
                const $box = $(this);
                const itemId = $box.data("item-id") || "";
                const planId = itemId.replace("pricing-plan-", "") || ["basic", "standard", "premium"][index];
                const isHighlighted = $box.hasClass("highlighted-box");
                
                const name = $box.find(".pricing-header h3").text() || "";
                const priceText = $box.find(".pricing-header h2").clone().children().remove().end().text().trim() || "";
                const period = $box.find(".pricing-header h2 sub").text() || "/per month";
                const btnText = $box.find(".pricing-btn a").text() || "let's get started";
                const btnLink = $box.find(".pricing-btn a").attr("href") || "#";
                
                const features = [];
                $box.find(".pricing-list ul li").each(function () {
                    features.push($(this).text());
                });
                
                plans.push({
                    id: planId,
                    name: name,
                    price: priceText,
                    period: period,
                    highlighted: isHighlighted,
                    btnText: btnText,
                    btnLink: btnLink,
                    features: features
                });
            });
            
            // Generate tabs for each plan
            let tabButtons = "";
            let tabContents = "";
            
            plans.forEach((plan, index) => {
                const isActive = index === 0;
                const highlightBadge = plan.highlighted ? '<span style="background: #ffcc01; color: #000; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 8px;">POPULAR</span>' : '';
                
                tabButtons += `
                    <button type="button" class="vcms-tab-btn ${isActive ? 'active' : ''}" data-tab="plan-${plan.id}" 
                        style="padding: 10px 20px; border: none; background: ${isActive ? '#ffcc01' : '#333'}; color: ${isActive ? '#000' : '#fff'}; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600;">
                        ${self.escapeHTML(plan.name)} ${highlightBadge}
                    </button>
                `;
                
                // Generate features HTML
                let featuresHTML = "";
                plan.features.forEach((feature, fIndex) => {
                    featuresHTML += `
                        <div class="pricing-feature-item" data-plan="${plan.id}" data-index="${fIndex + 1}" style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                            <input type="text" class="vcms-input" name="plan_${plan.id}_feature_${fIndex + 1}" value="${self.escapeHTML(feature)}" placeholder="Feature ${fIndex + 1}" style="flex: 1; margin: 0;">
                            <button type="button" class="btn" onclick="VCMSAdmin.deletePricingFeature('${plan.id}', ${fIndex + 1})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 10px; border-radius: 6px; font-size: 11px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                });
                
                tabContents += `
                    <div class="vcms-tab-content" data-tab-content="plan-${plan.id}" style="display: ${isActive ? 'block' : 'none'}; padding: 20px; background: #1a1a1a; border-radius: 0 0 12px 12px; border: 1px solid #333; border-top: none;">
                        <!-- Plan Basic Info -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-tag"></i> Plan Name</label>
                                <input type="text" class="vcms-input" name="plan_${plan.id}_name" value="${self.escapeHTML(plan.name)}">
                            </div>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-money-bill"></i> Price</label>
                                <input type="text" class="vcms-input" name="plan_${plan.id}_price" value="${self.escapeHTML(plan.price)}" placeholder="Rp 500 Rb">
                            </div>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-clock"></i> Period</label>
                                <input type="text" class="vcms-input" name="plan_${plan.id}_period" value="${self.escapeHTML(plan.period)}" placeholder="/per month">
                            </div>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-star"></i> Highlight Plan</label>
                                <select class="vcms-input" name="plan_${plan.id}_highlighted" style="padding: 10px;">
                                    <option value="0" ${!plan.highlighted ? 'selected' : ''}>Normal</option>
                                    <option value="1" ${plan.highlighted ? 'selected' : ''}>Highlighted (Popular)</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Button Settings -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; padding: 15px; background: #252525; border-radius: 8px;">
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-mouse-pointer"></i> Button Text</label>
                                <input type="text" class="vcms-input" name="plan_${plan.id}_btn_text" value="${self.escapeHTML(plan.btnText)}">
                            </div>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label><i class="fa-solid fa-link"></i> Button Link</label>
                                <input type="text" class="vcms-input" name="plan_${plan.id}_btn_link" value="${self.escapeHTML(plan.btnLink)}" placeholder="#">
                            </div>
                        </div>
                        
                        <!-- Features List -->
                        <div style="background: #252525; padding: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <label style="font-weight: 600; color: #ffcc01;"><i class="fa-solid fa-list-check"></i> Features</label>
                                <button type="button" class="btn" onclick="VCMSAdmin.addPricingFeature('${plan.id}')" style="background: rgba(76, 175, 80, 0.2); color: #4CAF50; border: 1px solid #4CAF50; padding: 6px 12px; border-radius: 6px; font-size: 12px;">
                                    <i class="fa-solid fa-plus"></i> Add Feature
                                </button>
                            </div>
                            <div id="features-container-${plan.id}">
                                ${featuresHTML || '<div class="no-features-msg" style="text-align: center; padding: 15px; color: #888;">No features. Click "Add Feature" to add one.</div>'}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return `
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-tags"></i> Edit Pricing Plans</h4>
                    
                    <!-- Tab Buttons -->
                    <div class="vcms-tabs" style="display: flex; gap: 5px; flex-wrap: wrap;">
                        ${tabButtons}
                    </div>
                    
                    <!-- Tab Contents -->
                    ${tabContents}
                </div>
            `;
        },

        // Add new pricing feature
        addPricingFeature: function (planId) {
            const container = $(`#features-container-${planId}`);
            container.find(".no-features-msg").remove();
            
            let maxIndex = 0;
            container.find(`.pricing-feature-item[data-plan="${planId}"]`).each(function() {
                const idx = parseInt($(this).data("index")) || 0;
                if (idx > maxIndex) maxIndex = idx;
            });
            
            const newIndex = maxIndex + 1;
            const html = `
                <div class="pricing-feature-item" data-plan="${planId}" data-index="${newIndex}" style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                    <input type="text" class="vcms-input" name="plan_${planId}_feature_${newIndex}" value="" placeholder="Feature ${newIndex}" style="flex: 1; margin: 0;">
                    <button type="button" class="btn" onclick="VCMSAdmin.deletePricingFeature('${planId}', ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 10px; border-radius: 6px; font-size: 11px;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            container.append(html);
            container.find(`.pricing-feature-item[data-index="${newIndex}"] input`).focus();
            this.showNotification("Feature added", "success");
        },

        // Delete pricing feature
        deletePricingFeature: function (planId, index) {
            if (!confirm("Delete this feature?")) return;
            
            const $item = $(`.pricing-feature-item[data-plan="${planId}"][data-index="${index}"]`);
            
            // Add hidden marker for deletion
            $(".vcms-edit-modal-body").append(
                `<input type="hidden" name="delete_plan_${planId}_feature_${index}" value="1">`
            );
            
            $item.css({
                "background": "#ffebee",
                "border-color": "#f44336",
                "opacity": "0.5"
            });
            
            setTimeout(() => {
                $item.slideUp(200, function() {
                    $(this).remove();
                    const container = $(`#features-container-${planId}`);
                    if (container.find(".pricing-feature-item").length === 0) {
                        container.html('<div class="no-features-msg" style="text-align: center; padding: 15px; color: #888;">No features. Click "Add Feature" to add one.</div>');
                    }
                });
            }, 200);
            
            this.showNotification("Feature will be deleted on save", "info");
        },

        // Generator untuk Pricing Benefits Form
        generatePricingBenefitsForm: function (element) {
            const self = this;
            const benefits = [];
            
            element.find("ul li").each(function (index) {
                const $li = $(this);
                const icon = $li.find("img").attr("src") || "";
                const text = $li.text().trim() || "";
                benefits.push({ icon, text, index: index + 1 });
            });
            
            let benefitsHTML = "";
            benefits.forEach((benefit) => {
                benefitsHTML += `
                    <div style="display: grid; grid-template-columns: 80px 1fr; gap: 15px; align-items: center; padding: 15px; background: #252525; border-radius: 8px; margin-bottom: 10px;">
                        <div style="text-align: center;">
                            <img src="${benefit.icon}" style="width: 40px; height: 40px; margin-bottom: 8px;">
                            <input type="file" class="vcms-input" name="benefit_${benefit.index}_icon" accept="image/*" style="font-size: 10px; padding: 4px;">
                        </div>
                        <div class="vcms-form-group" style="margin: 0;">
                            <label>Benefit ${benefit.index} Text</label>
                            <input type="text" class="vcms-input" name="benefit_${benefit.index}_text" value="${self.escapeHTML(benefit.text)}">
                        </div>
                    </div>
                `;
            });
            
            return `
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-gift"></i> Edit Benefits</h4>
                    ${benefitsHTML}
                    <div style="margin-top: 15px; padding: 12px; background: rgba(255, 204, 1, 0.1); border-radius: 8px; border-left: 4px solid #ffcc01;">
                        <p style="margin: 0; color: #e0e0e0; font-size: 12px;">
                            <i class="fa-solid fa-info-circle" style="color: #ffcc01; margin-right: 8px;"></i>
                            Icons should be SVG or small PNG files for best quality.
                        </p>
                    </div>
                </div>
            `;
        },

        // Generator untuk Contact Header Form
        generateContactHeaderForm: function (element) {
            const self = this;
            const title = element.find(".page-header-box h1").html() || "Contact <span>us</span>";
            
            return `
                <div class="vcms-form-group">
                    <label><i class="fa-solid fa-heading"></i> Page Title</label>
                    <input type="text" class="vcms-input" name="title" value="${self.stripHTML(title)}">
                    <small style="color: #888;">Use &lt;span&gt; for highlighted text. Example: Contact &lt;span&gt;us&lt;/span&gt;</small>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: rgba(255, 204, 1, 0.1); border-radius: 8px; border-left: 4px solid #ffcc01;">
                    <p style="margin: 0; color: #e0e0e0; font-size: 13px;">
                        <i class="fa-solid fa-info-circle" style="color: #ffcc01; margin-right: 8px;"></i>
                        Breadcrumb navigation is automatically generated based on the page structure.
                    </p>
                </div>
            `;
        },

        // Generator untuk Contact Section Form (Info + Form Settings)
        generateContactSectionForm: function (element) {
            const self = this;
            
            // Get section title
            const subTitle = element.find(".contact-us-content .section-title h3").first().text() || "contact us";
            const mainTitle = element.find(".contact-us-content .section-title h2").first().html() || "Let's start a <span>conversation</span>";
            
            // Get contact info
            const phoneLabel = element.find("[data-item-id='contact-phone'] .contact-info-content h3").text() || "phone number";
            const phoneValue = element.find("[data-item-id='contact-phone'] .contact-info-content p").text() || "+987 654 3210";
            
            const emailLabel = element.find("[data-item-id='contact-email'] .contact-info-content h3").text() || "email us";
            const emailValue = element.find("[data-item-id='contact-email'] .contact-info-content p").text() || "info@domain.com";
            
            const addressLabel = element.find("[data-item-id='contact-address'] .contact-info-content h3").text() || "address";
            const addressValue = element.find("[data-item-id='contact-address'] .contact-info-content p").text() || "7676 Arden Avenue, Ginashire";
            
            // Get form settings
            const formTitle = element.find(".contact-us-form .section-title h2").html() || "Get a <span>free advise</span>";
            const formBtnText = element.find(".contact-us-form .btn-default").text() || "send message";
            
            return `
                <div class="vcms-tabs-container">
                    <div class="vcms-tabs-header" style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                        <button type="button" class="vcms-tab-btn active" data-tab="contact-info" style="background: #ffcc01; color: #161616; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-address-card"></i> Contact Info
                        </button>
                        <button type="button" class="vcms-tab-btn" data-tab="contact-form" style="background: #333; color: #fff; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-paper-plane"></i> Form Settings
                        </button>
                    </div>
                    
                    <!-- Contact Info Tab -->
                    <div class="vcms-tab-content active" data-tab-content="contact-info">
                        <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-heading"></i> Section Title</h5>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Sub Title</label>
                                    <input type="text" class="vcms-input" name="sub_title" value="${self.escapeHTML(subTitle)}">
                                </div>
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Main Title</label>
                                    <input type="text" class="vcms-input" name="main_title" value="${self.stripHTML(mainTitle)}">
                                    <small style="color: #888;">Use &lt;span&gt; for highlight</small>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-phone"></i> Phone</h5>
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Label</label>
                                    <input type="text" class="vcms-input" name="phone_label" value="${self.escapeHTML(phoneLabel)}">
                                </div>
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Phone Number</label>
                                    <input type="text" class="vcms-input" name="phone_value" value="${self.escapeHTML(phoneValue)}">
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-envelope"></i> Email</h5>
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Label</label>
                                    <input type="text" class="vcms-input" name="email_label" value="${self.escapeHTML(emailLabel)}">
                                </div>
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Email Address</label>
                                    <input type="email" class="vcms-input" name="email_value" value="${self.escapeHTML(emailValue)}">
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #252525; padding: 20px; border-radius: 12px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-location-dot"></i> Address</h5>
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Label</label>
                                    <input type="text" class="vcms-input" name="address_label" value="${self.escapeHTML(addressLabel)}">
                                </div>
                                <div class="vcms-form-group" style="margin: 0;">
                                    <label>Full Address</label>
                                    <input type="text" class="vcms-input" name="address_value" value="${self.escapeHTML(addressValue)}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Settings Tab -->
                    <div class="vcms-tab-content" data-tab-content="contact-form" style="display: none;">
                        <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-heading"></i> Form Title</h5>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label>Title</label>
                                <input type="text" class="vcms-input" name="form_title" value="${self.stripHTML(formTitle)}">
                                <small style="color: #888;">Use &lt;span&gt; for highlight. Example: Get a &lt;span&gt;free advise&lt;/span&gt;</small>
                            </div>
                        </div>
                        
                        <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                            <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-mouse-pointer"></i> Button</h5>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label>Button Text</label>
                                <input type="text" class="vcms-input" name="form_btn_text" value="${self.escapeHTML(formBtnText)}">
                            </div>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); padding: 20px; border-radius: 12px;">
                            <h5 style="color: #fff; margin-bottom: 15px;"><i class="fa-brands fa-whatsapp"></i> WhatsApp Integration</h5>
                            <div class="vcms-form-group" style="margin: 0;">
                                <label style="color: #fff;">WhatsApp Number</label>
                                <input type="text" class="vcms-input" name="whatsapp_number" value="" placeholder="6281234567890" style="background: rgba(255,255,255,0.9); color: #000;">
                                <small style="color: rgba(255,255,255,0.8);">Format: Country code + number without + or spaces. Example: 6281234567890</small>
                            </div>
                            <div style="margin-top: 15px; padding: 12px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                                <p style="margin: 0; color: #fff; font-size: 12px;">
                                    <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i>
                                    When visitors submit the contact form, they will be redirected to WhatsApp with pre-filled message.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        // Generator untuk Contact Form Settings (individual item)
        generateContactFormSettingsForm: function (element) {
            const self = this;
            
            const formTitle = element.find(".section-title h2").html() || "Get a <span>free advise</span>";
            const formBtnText = element.find(".btn-default").text() || "send message";
            
            return `
                <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-heading"></i> Form Title</h5>
                    <div class="vcms-form-group" style="margin: 0;">
                        <label>Title</label>
                        <input type="text" class="vcms-input" name="form_title" value="${self.stripHTML(formTitle)}">
                        <small style="color: #888;">Use &lt;span&gt; for highlight</small>
                    </div>
                </div>
                
                <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-mouse-pointer"></i> Button</h5>
                    <div class="vcms-form-group" style="margin: 0;">
                        <label>Button Text</label>
                        <input type="text" class="vcms-input" name="form_btn_text" value="${self.escapeHTML(formBtnText)}">
                    </div>
                </div>
                
                <div style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); padding: 20px; border-radius: 12px;">
                    <h5 style="color: #fff; margin-bottom: 15px;"><i class="fa-brands fa-whatsapp"></i> WhatsApp Integration</h5>
                    <div class="vcms-form-group" style="margin: 0;">
                        <label style="color: #fff;">WhatsApp Number</label>
                        <input type="text" class="vcms-input" name="whatsapp_number" value="" placeholder="6281234567890" style="background: rgba(255,255,255,0.9); color: #000;">
                        <small style="color: rgba(255,255,255,0.8);">Format: Country code + number. Example: 6281234567890</small>
                    </div>
                </div>
            `;
        },

        // Generator untuk Google Map Section Form
        generateMapSectionForm: function (element) {
            const self = this;
            const currentMapUrl = element.find("iframe").attr("src") || "";
            
            return `
                <div style="margin-bottom: 20px;">
                    <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-map-location-dot"></i> Google Maps Settings</h4>
                    
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                        <div class="vcms-form-group" style="margin: 0;">
                            <label><i class="fa-solid fa-link"></i> Google Maps Embed URL</label>
                            <textarea class="vcms-input" name="embed_url" rows="4" style="font-family: monospace; font-size: 12px;" placeholder="https://www.google.com/maps/embed?pb=...">${self.escapeHTML(currentMapUrl)}</textarea>
                        </div>
                    </div>
                    
                    <div style="background: #1a1a1a; padding: 20px; border-radius: 12px; border: 1px solid #333;">
                        <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-circle-question"></i> Cara Mendapatkan Link Embed Google Maps</h5>
                        <ol style="color: #ccc; font-size: 13px; padding-left: 20px; margin: 0;">
                            <li style="margin-bottom: 10px;">Buka <a href="https://www.google.com/maps" target="_blank" style="color: #4285F4;">Google Maps</a></li>
                            <li style="margin-bottom: 10px;">Cari lokasi yang diinginkan</li>
                            <li style="margin-bottom: 10px;">Klik tombol <strong>Share</strong> (Bagikan)</li>
                            <li style="margin-bottom: 10px;">Pilih tab <strong>"Embed a map"</strong> (Sematkan peta)</li>
                            <li style="margin-bottom: 10px;">Klik <strong>"Copy HTML"</strong></li>
                            <li style="margin-bottom: 0;">Paste di sini, lalu ambil hanya URL yang ada di dalam <code style="background: #333; padding: 2px 6px; border-radius: 4px;">src="..."</code></li>
                        </ol>
                    </div>
                    
                    <div style="margin-top: 20px; background: #252525; padding: 15px; border-radius: 12px;">
                        <label style="color: #ffcc01; font-weight: 600; margin-bottom: 10px; display: block;"><i class="fa-solid fa-eye"></i> Preview</label>
                        <div id="map-preview" style="width: 100%; height: 200px; border-radius: 8px; overflow: hidden; background: #1a1a1a;">
                            <iframe src="${self.escapeHTML(currentMapUrl)}" style="width: 100%; height: 100%; border: none;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            `;
        },

        // Generator untuk About Section (Image + Content combined)
        generateAboutSectionForm: function (element) {
            const self = this;
            
            // Get image
            const currentImg = element.find(".about-us-img img").attr("src") || "images/about-img.jpg";
            const defaultImg = "images/about-img.jpg";
            
            // Get content values
            const subTitle = element.find(".section-title h3").text() || "about us";
            const mainTitle = element.find(".section-title h2").html() || "A team of <span>creative thinkers</span>";
            const description = element.find(".section-title p").text() || "";
            
            // Get list items
            const listItems = [];
            element.find(".about-us-info-list li").each(function() {
                listItems.push($(this).text());
            });
            
            // Generate list items HTML
            let listItemsHTML = "";
            listItems.forEach((item, index) => {
                listItemsHTML += self.generateAboutListItemHTML(index, item);
            });
            
            return `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Left Column: Image -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                        <h5 style="margin: 0 0 15px 0; color: #ffcc01; font-weight: 600;">
                            <i class="fa-solid fa-image"></i> About Image
                        </h5>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <img id="about-img-preview" src="${currentImg}" style="max-height: 180px; border-radius: 8px; border: 1px solid #444;">
                        </div>
                        <input type="hidden" name="image_current" value="${self.escapeHTML(currentImg)}">
                        <input type="hidden" name="image_default" value="${defaultImg}">
                        <input type="file" class="vcms-input" name="image" accept="image/*" data-preview="about-img-preview" style="font-size: 12px;">
                        <small style="color: #888; display: block; margin-top: 8px;">Recommended: 600x700px</small>
                        
                        <button type="button" class="btn" onclick="VCMSAdmin.resetAboutImage('${defaultImg}')" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 12px; border-radius: 6px; width: 100%; margin-top: 10px; font-size: 12px;">
                            <i class="fa-solid fa-undo"></i> Reset to Default
                        </button>
                    </div>
                    
                    <!-- Right Column: Content -->
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div class="vcms-form-group" style="margin: 0;">
                            <label><i class="fa-solid fa-tag"></i> Sub Title</label>
                            <input type="text" class="vcms-input" name="sub_title" value="${self.escapeHTML(subTitle)}">
                        </div>
                        
                        <div class="vcms-form-group" style="margin: 0;">
                            <label><i class="fa-solid fa-heading"></i> Main Title</label>
                            <input type="text" class="vcms-input" name="main_title" value="${self.stripHTML(mainTitle)}">
                            <small style="color: #888;">Use &lt;span&gt; for highlighted text</small>
                        </div>
                        
                        <div class="vcms-form-group" style="margin: 0;">
                            <label><i class="fa-solid fa-align-left"></i> Description</label>
                            <textarea class="vcms-textarea" name="description" rows="3">${self.escapeHTML(description)}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Feature List -->
                <div style="margin-top: 20px; background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h5 style="margin: 0; color: #ffcc01; font-weight: 600;">
                            <i class="fa-solid fa-list"></i> Feature List Items
                        </h5>
                        <button type="button" class="btn" onclick="VCMSAdmin.addAboutListItem()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; border: none; padding: 6px 15px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                            <i class="fa-solid fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div id="about-list-items-container" style="display: flex; flex-direction: column; gap: 10px;">
                        ${listItemsHTML || '<div class="no-items-msg" style="text-align: center; padding: 20px; color: #888;"><i class="fa-solid fa-info-circle"></i> No items yet. Click "Add Item" to add one.</div>'}
                    </div>
                </div>
            `;
        },
        
        // Generate HTML for a single about list item
        generateAboutListItemHTML: function(index, value) {
            return `
                <div class="about-list-item" data-index="${index}" style="display: flex; align-items: center; gap: 10px; background: #1a1a1a; padding: 10px; border-radius: 8px; border: 1px solid #333;">
                    <span style="color: #ffcc01; font-weight: 600; min-width: 25px;">${index + 1}.</span>
                    <input type="text" class="vcms-input" name="list_item_${index}" value="${this.escapeHTML(value)}" placeholder="Feature item" style="flex: 1; margin: 0;">
                    <button type="button" class="btn" onclick="VCMSAdmin.deleteAboutListItem(${index})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 10px; border-radius: 6px; font-size: 11px;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
        },
        
        // Add new about list item
        addAboutListItem: function() {
            const container = $("#about-list-items-container");
            
            // Remove "no items" message if exists
            container.find(".no-items-msg").remove();
            
            // Find next available index
            let maxIndex = -1;
            container.find(".about-list-item").each(function() {
                const idx = parseInt($(this).data("index"), 10);
                if (!isNaN(idx) && idx > maxIndex) {
                    maxIndex = idx;
                }
            });
            const newIndex = maxIndex + 1;
            
            // Add new item
            const newItemHTML = this.generateAboutListItemHTML(newIndex, "");
            container.append(newItemHTML);
            
            // Focus on new input
            container.find(`.about-list-item[data-index="${newIndex}"] input`).focus();
            
            this.showNotification("New item added", "success");
        },
        
        // Delete about list item
        deleteAboutListItem: function(index) {
            const container = $("#about-list-items-container");
            const $item = container.find(`.about-list-item[data-index="${index}"]`);
            
            // Animate and remove
            $item.css({
                "background": "#ffebee",
                "border-color": "#f44336",
                "opacity": "0.5"
            });
            
            setTimeout(() => {
                $item.slideUp(200, function() {
                    $(this).remove();
                    
                    // Show "no items" message if empty
                    if (container.find(".about-list-item").length === 0) {
                        container.html('<div class="no-items-msg" style="text-align: center; padding: 20px; color: #888;"><i class="fa-solid fa-info-circle"></i> No items yet. Click "Add Item" to add one.</div>');
                    }
                });
            }, 150);
            
            // Add delete marker for server
            $(".vcms-edit-modal-body").append(`<input type="hidden" name="delete_list_item_${index}" value="1">`);
        },

        // Reset about image to default
        resetAboutImage: function(defaultImg) {
            $("#about-img-preview").attr("src", "/" + defaultImg);
            $("input[name='image_current']").val(defaultImg);
            $("input[name='image']").val("");
            
            // Add reset marker
            if ($("input[name='reset_image']").length === 0) {
                $(".vcms-edit-modal-body").append(`<input type="hidden" name="reset_image" value="${defaultImg}">`);
            } else {
                $("input[name='reset_image']").val(defaultImg);
            }
            
            this.showNotification("Image will be reset to default on save", "info");
        },

        // Generator untuk Vision Mission Section
        generateVisionMissionForm: function (element) {
            const self = this;
            
            // Get vision data
            const visionItem = element.find(".vision-mission-item").eq(0);
            const visionImg = visionItem.find(".vision-mission-image img").attr("src") || "images/our-vision-img.jpg";
            const visionSubTitle = visionItem.find(".section-title h3").text() || "our vision";
            const visionTitle = visionItem.find(".section-title h2").html() || "Driving the Future of <span>Creativity</span>";
            const visionDesc = visionItem.find(".section-title p").text() || "";
            const visionList = [];
            visionItem.find(".vision-mission-list li").each(function() {
                visionList.push($(this).text());
            });
            
            // Get mission data
            const missionItem = element.find(".vision-mission-item").eq(1);
            const missionImg = missionItem.find(".vision-mission-image img").attr("src") || "images/our-mission-img.jpg";
            const missionSubTitle = missionItem.find(".section-title h3").text() || "our mission";
            const missionTitle = missionItem.find(".section-title h2").html() || "Bringing ideas to life through <span>creativity</span>";
            const missionDesc = missionItem.find(".section-title p").text() || "";
            const missionList = [];
            missionItem.find(".vision-mission-list li").each(function() {
                missionList.push($(this).text());
            });
            
            // Generate list items HTML
            let visionListHTML = "";
            visionList.forEach((item, index) => {
                visionListHTML += self.generateVMListItemHTML("vision", index, item);
            });
            
            let missionListHTML = "";
            missionList.forEach((item, index) => {
                missionListHTML += self.generateVMListItemHTML("mission", index, item);
            });
            
            return `
                <div class="vcms-tabs-container">
                    <div class="vcms-tabs-header" style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                        <button type="button" class="vcms-tab-btn active" data-tab="vision" style="background: #ffcc01; color: #161616; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-eye"></i> Our Vision
                        </button>
                        <button type="button" class="vcms-tab-btn" data-tab="mission" style="background: #333; color: #fff; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-rocket"></i> Our Mission
                        </button>
                    </div>
                    
                    <!-- Vision Tab -->
                    <div class="vcms-tab-content active" data-tab-content="vision">
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-image"></i> Vision Image</label>
                            <div class="vcms-image-upload-wrapper" style="display: flex; gap: 15px; align-items: flex-start;">
                                <div style="width: 150px; height: 100px; background: #252525; border-radius: 8px; overflow: hidden; border: 1px solid #444;">
                                    <img id="vision-img-preview" src="${visionImg}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" class="vcms-input" name="vision_image" accept="image/*" data-preview="vision-img-preview" style="margin-bottom: 8px;">
                                    <input type="hidden" name="vision_image_current" value="${self.escapeHTML(visionImg)}">
                                    <small style="color: #888;">Recommended: 600x400px</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-tag"></i> Sub Title</label>
                            <input type="text" class="vcms-input" name="vision_sub_title" value="${self.escapeHTML(visionSubTitle)}">
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-heading"></i> Main Title</label>
                            <input type="text" class="vcms-input" name="vision_title" value="${self.stripHTML(visionTitle)}">
                            <small style="color: #888;">Use &lt;span&gt; for highlighted text</small>
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-align-left"></i> Description</label>
                            <textarea class="vcms-textarea" name="vision_description" rows="3">${self.escapeHTML(visionDesc)}</textarea>
                        </div>
                        
                        <div class="vcms-form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label style="margin: 0;"><i class="fa-solid fa-list"></i> List Items</label>
                                <button type="button" class="btn" onclick="VCMSAdmin.addVMListItem('vision')" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; border: none; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div id="vision-list-container" style="display: flex; flex-direction: column; gap: 8px;">
                                ${visionListHTML || '<div class="no-items-msg" style="text-align: center; padding: 15px; color: #888; font-size: 12px;"><i class="fa-solid fa-info-circle"></i> No items. Click "Add" to add one.</div>'}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mission Tab -->
                    <div class="vcms-tab-content" data-tab-content="mission" style="display: none;">
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-image"></i> Mission Image</label>
                            <div class="vcms-image-upload-wrapper" style="display: flex; gap: 15px; align-items: flex-start;">
                                <div style="width: 150px; height: 100px; background: #252525; border-radius: 8px; overflow: hidden; border: 1px solid #444;">
                                    <img id="mission-img-preview" src="${missionImg}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" class="vcms-input" name="mission_image" accept="image/*" data-preview="mission-img-preview" style="margin-bottom: 8px;">
                                    <input type="hidden" name="mission_image_current" value="${self.escapeHTML(missionImg)}">
                                    <small style="color: #888;">Recommended: 600x400px</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-tag"></i> Sub Title</label>
                            <input type="text" class="vcms-input" name="mission_sub_title" value="${self.escapeHTML(missionSubTitle)}">
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-heading"></i> Main Title</label>
                            <input type="text" class="vcms-input" name="mission_title" value="${self.stripHTML(missionTitle)}">
                            <small style="color: #888;">Use &lt;span&gt; for highlighted text</small>
                        </div>
                        
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-align-left"></i> Description</label>
                            <textarea class="vcms-textarea" name="mission_description" rows="3">${self.escapeHTML(missionDesc)}</textarea>
                        </div>
                        
                        <div class="vcms-form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <label style="margin: 0;"><i class="fa-solid fa-list"></i> List Items</label>
                                <button type="button" class="btn" onclick="VCMSAdmin.addVMListItem('mission')" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; border: none; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div id="mission-list-container" style="display: flex; flex-direction: column; gap: 8px;">
                                ${missionListHTML || '<div class="no-items-msg" style="text-align: center; padding: 15px; color: #888; font-size: 12px;"><i class="fa-solid fa-info-circle"></i> No items. Click "Add" to add one.</div>'}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        // Generate HTML untuk satu VM list item
        generateVMListItemHTML: function (type, index, value) {
            return `
                <div class="vcms-vm-list-item" data-type="${type}" data-index="${index}" style="display: flex; gap: 8px; align-items: center;">
                    <input type="text" class="vcms-input" name="${type}_list_${index}" value="${this.escapeHTML(value)}" placeholder="List item" style="flex: 1;">
                    <button type="button" class="btn" onclick="VCMSAdmin.deleteVMListItem('${type}', ${index})" style="background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 11px;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
        },
        
        // Add new VM list item
        addVMListItem: function (type) {
            const container = $(`#${type}-list-container`);
            
            // Remove no-items message if exists
            container.find(".no-items-msg").remove();
            
            // Find max index
            let maxIndex = -1;
            container.find(`.vcms-vm-list-item[data-type="${type}"]`).each(function() {
                const idx = parseInt($(this).data("index")) || 0;
                if (idx > maxIndex) maxIndex = idx;
            });
            
            const newIndex = maxIndex + 1;
            const html = this.generateVMListItemHTML(type, newIndex, "");
            container.append(html);
            
            // Focus on new input
            container.find(`.vcms-vm-list-item[data-index="${newIndex}"] input`).focus();
            
            this.showNotification("New item added", "success");
        },
        
        // Delete VM list item
        deleteVMListItem: function (type, index) {
            if (!confirm("Delete this item?")) return;
            
            const $item = $(`.vcms-vm-list-item[data-type="${type}"][data-index="${index}"]`);
            
            // Add hidden marker for deletion
            $(".vcms-edit-modal-body").append(
                `<input type="hidden" name="delete_vm_list_${type}_${index}" value="1">`
            );
            
            // Animate and remove
            $item.css({
                "background": "#ffebee",
                "border-color": "#f44336",
                "opacity": "0.5"
            });
            
            setTimeout(() => {
                $item.slideUp(200, function() {
                    $(this).remove();
                    
                    // Show no-items message if empty
                    const container = $(`#${type}-list-container`);
                    if (container.find(".vcms-vm-list-item").length === 0) {
                        container.html('<div class="no-items-msg" style="text-align: center; padding: 15px; color: #888; font-size: 12px;"><i class="fa-solid fa-info-circle"></i> No items. Click "Add" to add one.</div>');
                    }
                });
            }, 200);
            
            this.showNotification("Item will be deleted on save", "info");
        },

        generateGenericForm: function (element) {
            return `
                <div class="vcms-form-group">
                    <label>Section Content</label>
                    <textarea class="vcms-textarea" name="content" rows="8">Content editing for this section type coming soon...</textarea>
                </div>
                <div class="alert alert-info">
                    <strong>Note:</strong> This section type requires custom implementation. 
                    Please contact developer for specific editing interface.
                </div>
            `;
        },
     generateTrustedClientsForm: function (element) {
    // 1. Ambil Judul Clients dari elemen h3 di dalam container section.
    const titleElement = element.find("h3"); 
    const currentTitle = titleElement.text().trim() || "";
    
    // 🔥 CONSOLE LOG UNTUK MENDIAGNOSA PENGAMBILAN DATA
    console.log("VCMS DIAGNOSIS: Current Title read from DOM:", currentTitle);
    
    // ... (lanjutan kode) ...
    // Di dalam HTML yang dikembalikan, tambahkan juga log:
    return `
        <div class="vcms-form-group">
            <label>Section Title</label>
            <input 
                type="text" 
                class="vcms-input" 
                name="section_title_text" 
                value="${this.escapeHTML(currentTitle)}" 
            >
            <small>This controls the main title text for the Trusted Clients section.</small>
        </div>
        
        <div class="alert alert-info" style="/* ... */">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Current Value Read:</strong> "${currentTitle}"  <br>
            Note: This form is restricted to editing only the section title.
        </div>
    `;
},
        generateServicesSectionForm: function (element) {
            const subTitle =
                element.find(".section-title h3").text().trim() || "";
            const btnText = element.find(".section-btn a").text().trim() || "";
            const btnLink = element.find(".section-btn a").attr("href") || "#";

            // === LOGIKA PEMBERSIH ANIMASI (CLEANER) ===
            // 1. Clone elemen judul supaya halaman asli tidak rusak
            const mainTitleEl = element.find(".section-title h2").clone();

            // 2. Simpan dulu isi <span> (misal: "services") dan ganti dengan penanda sementara
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text()); // Ambil teks murni di dalam span
                $(this).replaceWith(`###SPAN${index}###`); // Ganti span dengan marker unik
            });

            // 3. Ambil .text() dari elemen sisa.
            // Fungsi .text() otomatis akan membuang semua <div style="..."> sampah animasi
            let cleanMainTitle = mainTitleEl.text();

            // 4. Kembalikan <span> ke posisi semula
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });

            // Bersihkan spasi berlebih akibat pemecahan div
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();
            // ==========================================

            return `
                <div class="vcms-form-group">
                    <label>Sub Title (Small)</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Main Title (Big)</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>You can use &lt;span&gt;text&lt;/span&gt; to highlight words.</small>
                </div>

                <div class="vcms-form-group">
                    <label>Button Text</label>
                    <input type="text" class="vcms-input" name="btn_text" value="${this.escapeHTML(
                        btnText
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Button Link (URL)</label>
                    <input type="text" class="vcms-input" name="btn_link" value="${this.escapeHTML(
                        btnLink
                    )}">
                </div>

                <div class="alert alert-info" style="margin-top: 15px; padding: 10px; background: #e3f2fd; border-radius: 4px; font-size: 0.9em;">
                    <i class="fa-solid fa-info-circle"></i> 
                    <strong>Note:</strong> To edit the service tabs content, please click the 
                    <i class="fa-solid fa-list-check"></i> icon located above the tabs.
                </div>
            `;
        },
        generateServiceListForm: function (element) {
            let listHTML = "";
            const self = this;
            
            this.servicesData = [];

            // Detect structure type
            const isTabStructure = element.find(".tab-pane").length > 0;
            const serviceItems = isTabStructure ? element.find(".tab-pane") : element.find(".service-item");

            serviceItems.each(function (index) {
                const item = $(this);
                let id, title, desc, imgSrc, iconSrc, link;

                if (isTabStructure) {
                    id = item.attr("id");
                    title = item.find(".service-box-item-content h3 a").text().trim();
                    desc = item.find(".service-box-item-content p").text().trim();
                    imgSrc = item.find(".service-box-image img").attr("src") || "";
                    iconSrc = item.find(".icon-box img").attr("src") || "";
                    link = item.find(".service-box-item-content h3 a").attr("href") || "";
                } else {
                    id = item.attr("data-service-id") || item.data("service-id") || ("0" + (index + 1));
                    title = item.find(".service-content h3 a").text().trim();
                    desc = item.find(".service-content p").text().trim();
                    imgSrc = item.find(".service-image img").attr("src") || "";
                    iconSrc = "";
                    link = item.find(".service-content h3 a").attr("href") || "";
                }

                self.servicesData.push({ id, title, desc, imgSrc, iconSrc, link, index });

                // Simplified form - only basic info, link to detail page for full editing
                listHTML += `
                    <div class="vcms-service-item" data-service-id="${id}" data-index="${index}" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <!-- Thumbnail -->
                            <div style="flex-shrink: 0; text-align: center;">
                                <img id="service-img-preview-${id}" src="${imgSrc}" style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #3a3a3a; margin-bottom: 8px;">
                                <input type="file" class="vcms-input vcms-service-image-file" name="service_${id}_image_file" accept="image/*" style="font-size: 10px; width: 120px;" data-preview="service-img-preview-${id}" data-service-id="${id}">
                                <input type="hidden" name="service_${id}_image" id="service-img-value-${id}" value="${self.escapeHTML(imgSrc)}">
                            </div>
                            
                            <!-- Info -->
                            <div style="flex: 1;">
                                <div class="vcms-form-group" style="margin-bottom: 10px;">
                                    <label style="font-size: 12px;">Service Title</label>
                                    <input type="text" class="vcms-input" name="service_${id}_title" value="${self.escapeHTML(title)}" style="font-size: 13px;">
                                </div>
                                <div class="vcms-form-group" style="margin-bottom: 0;">
                                    <label style="font-size: 12px;">Short Description</label>
                                    <textarea class="vcms-textarea" name="service_${id}_desc" rows="2" style="font-size: 13px;">${self.escapeHTML(desc)}</textarea>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div style="flex-shrink: 0; display: flex; flex-direction: column; gap: 8px;">
                                <a href="/admin/pages/service/${id}" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; text-align: center;">
                                    <i class="fa-solid fa-pen"></i> Edit Details
                                </a>
                                <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteService('${id}', ${index}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px; font-size: 12px;">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            const serviceCount = serviceItems.length;
            const isHomePage = isTabStructure;
            const noteText = isHomePage 
                ? '<strong>Note:</strong> Home page displays first 5 services only. Full editing available on Services page.'
                : '<strong>Note:</strong> Edit basic info here, then click "Edit Details" to edit benefits, process, FAQs, and CTA for each service.';

            const emptyState = serviceCount === 0 ? `
                <div style="text-align: center; padding: 60px 20px; color: #888;">
                    <i class="fa-solid fa-concierge-bell" style="font-size: 64px; margin-bottom: 20px; display: block; color: #555;"></i>
                    <p style="font-size: 16px; margin-bottom: 10px;">No services yet.</p>
                    <p style="font-size: 13px;">Click "Add Service" to create your first service.</p>
                </div>
            ` : listHTML;

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-layer-group" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="services-count">${serviceCount}</span> services</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addNewService()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none; box-shadow: 0 4px 15px rgba(255, 204, 1, 0.3);">
                        <i class="fa-solid fa-plus"></i> Add Service
                    </button>
                </div>

                <div class="vcms-scroll-wrapper" id="services-list-container" style="max-height: 60vh; overflow-y: auto; padding-right: 5px;">
                    ${emptyState}
                </div>

                <div style="margin-top: 20px; padding: 14px 18px; background: rgba(255, 152, 0, 0.1); border-left: 4px solid #ff9800; border-radius: 8px; font-size: 13px; color: #ffb74d;">
                    <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i> 
                    ${noteText}
                </div>
            `;
        },

        // Add service benefit
        addServiceBenefit: function(serviceId) {
            const container = $(`#service-benefits-list-${serviceId}`);
            const countInput = $(`#service-benefit-count-${serviceId}`);
            const newIndex = parseInt(countInput.val()) + 1;
            countInput.val(newIndex);
            
            const newHTML = `
                <div class="benefit-item" data-index="${newIndex}" style="display: flex; gap: 8px; margin-bottom: 8px;">
                    <input type="text" class="vcms-input" name="service_${serviceId}_benefit_${newIndex}" value="" placeholder="Benefit ${newIndex}" style="flex: 1; margin: 0;">
                    <button type="button" onclick="VCMSAdmin.deleteServiceBenefit('${serviceId}', ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                </div>`;
            container.append(newHTML);
        },

        // Delete service benefit
        deleteServiceBenefit: function(serviceId, index) {
            const container = $(`#service-benefits-list-${serviceId}`);
            container.find(`.benefit-item[data-index="${index}"]`).remove();
            // Add hidden input for deletion
            container.append(`<input type="hidden" name="delete_service_benefit_${serviceId}_${index}" value="1">`);
        },

        // Add service process
        addServiceProcess: function(serviceId) {
            const container = $(`#service-process-list-${serviceId}`);
            const countInput = $(`#service-process-count-${serviceId}`);
            const newIndex = parseInt(countInput.val()) + 1;
            countInput.val(newIndex);
            
            const newHTML = `
                <div class="process-item" data-index="${newIndex}" style="background: #1a1a1a; padding: 12px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #333;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="color: #ffcc01; font-weight: 600; font-size: 12px;">Process Step ${newIndex}</span>
                        <button type="button" onclick="VCMSAdmin.deleteServiceProcess('${serviceId}', ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;"><i class="fa-solid fa-times"></i></button>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <input type="text" class="vcms-input" name="service_${serviceId}_process_${newIndex}_icon" value="images/icon-design-process-1.svg" placeholder="Icon path" style="margin: 0; font-size: 12px;">
                        <input type="text" class="vcms-input" name="service_${serviceId}_process_${newIndex}_title" value="" placeholder="Title" style="margin: 0; font-size: 12px;">
                    </div>
                    <input type="text" class="vcms-input" name="service_${serviceId}_process_${newIndex}_desc" value="" placeholder="Description" style="margin: 8px 0 0 0; font-size: 12px;">
                </div>`;
            container.append(newHTML);
        },

        // Delete service process
        deleteServiceProcess: function(serviceId, index) {
            const container = $(`#service-process-list-${serviceId}`);
            container.find(`.process-item[data-index="${index}"]`).remove();
            container.append(`<input type="hidden" name="delete_service_process_${serviceId}_${index}" value="1">`);
        },

        // Add service FAQ
        addServiceFaq: function(serviceId) {
            const container = $(`#service-faq-list-${serviceId}`);
            const countInput = $(`#service-faq-count-${serviceId}`);
            const newIndex = parseInt(countInput.val()) + 1;
            countInput.val(newIndex);
            
            const newHTML = `
                <div class="faq-item" data-index="${newIndex}" style="background: #1a1a1a; padding: 12px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #333;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="color: #ffcc01; font-weight: 600; font-size: 12px;">FAQ ${newIndex}</span>
                        <button type="button" onclick="VCMSAdmin.deleteServiceFaq('${serviceId}', ${newIndex})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;"><i class="fa-solid fa-times"></i></button>
                    </div>
                    <input type="text" class="vcms-input" name="service_${serviceId}_faq_${newIndex}_question" value="" placeholder="Question" style="margin: 0 0 8px 0; font-size: 12px;">
                    <textarea class="vcms-textarea" name="service_${serviceId}_faq_${newIndex}_answer" placeholder="Answer" rows="2" style="margin: 0; font-size: 12px;"></textarea>
                </div>`;
            container.append(newHTML);
        },

        // Delete service FAQ
        deleteServiceFaq: function(serviceId, index) {
            const container = $(`#service-faq-list-${serviceId}`);
            container.find(`.faq-item[data-index="${index}"]`).remove();
            container.append(`<input type="hidden" name="delete_service_faq_${serviceId}_${index}" value="1">`);
        },

        // Generate Service Detail Form (for individual service editing)
        generateServiceDetailForm: function(element) {
            const self = this;
            const $item = $(element);
            const id = $item.data("service-id");
            
            // Get basic data
            const title = $item.data("title") || "";
            const desc = $item.data("desc") || "";
            const imgSrcRaw = $item.data("image") || "images/service-image-1.jpg";
            // Ensure image path has leading slash for display in img tag
            let imgSrc = imgSrcRaw;
            if (imgSrc && !imgSrc.startsWith('/') && !imgSrc.startsWith('http')) {
                imgSrc = '/' + imgSrc;
            }
            const link = $item.data("link") || "";
            
            // Get detail data
            const featureImage = $item.data("feature-image") || "";
            const desc1 = $item.data("desc1") || "";
            const desc2 = $item.data("desc2") || "";
            const betit = $item.data("betit") || "";
            const bedes = $item.data("bedes") || "";
            const benefitCount = parseInt($item.data("benefit-count")) || 0;
            const faqSubTitle = $item.data("faq-subtitle") || "";
            const fatit = $item.data("fatit") || "";
            const faqCount = parseInt($item.data("faq-count")) || 0;
            const ctaText = $item.data("cta-text") || "";
            const ctaEmail = $item.data("cta-email") || "";
            const ctaBtnText = $item.data("cta-btn-text") || "";

            // Build benefits list
            let benefitsListHTML = benefitCount === 0 
                ? '<div class="empty-state" style="text-align: center; color: #888; padding: 15px; font-size: 12px;">No benefits yet. Click Add Benefit.</div>'
                : '';
            for (let b = 1; b <= benefitCount; b++) {
                const val = $item.data("benefit-" + b) || "";
                benefitsListHTML += `
                    <div class="benefit-item" data-index="${b}" style="display: flex; gap: 8px; margin-bottom: 8px;">
                        <input type="text" class="vcms-input" name="service_${id}_benefit_${b}" value="${self.escapeHTML(val)}" placeholder="Benefit ${b}" style="flex: 1; margin: 0;">
                        <button type="button" onclick="VCMSAdmin.deleteServiceBenefit('${id}', ${b})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                    </div>`;
            }

            // Build FAQ items
            let faqListHTML = faqCount === 0 
                ? '<div class="empty-state" style="text-align: center; color: #888; padding: 15px; font-size: 12px;">No FAQs yet. Click Add FAQ.</div>'
                : '';
            for (let f = 1; f <= faqCount; f++) {
                const fQuestion = $item.data("faq-" + f + "-question") || "";
                const fAnswer = $item.data("faq-" + f + "-answer") || "";
                faqListHTML += `
                    <div class="faq-item" data-index="${f}" style="background: #1a1a1a; padding: 12px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #333;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="color: #ffcc01; font-weight: 600; font-size: 12px;">FAQ ${f}</span>
                            <button type="button" onclick="VCMSAdmin.deleteServiceFaq('${id}', ${f})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;"><i class="fa-solid fa-times"></i></button>
                        </div>
                        <input type="text" class="vcms-input" name="service_${id}_faq_${f}_question" value="${self.escapeHTML(fQuestion)}" placeholder="Question" style="margin: 0 0 8px 0; font-size: 12px;">
                        <textarea class="vcms-textarea" name="service_${id}_faq_${f}_answer" placeholder="Answer" rows="2" style="margin: 0; font-size: 12px;">${self.escapeHTML(fAnswer)}</textarea>
                    </div>`;
            }

            return `
                <div style="max-height: 70vh; overflow-y: auto; padding-right: 10px;">
                    <!-- Basic Info Section -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-info-circle"></i> Basic Information
                        </h4>
                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 20px;">
                            <div style="text-align: center;">
                                <label style="font-weight: 600; display: block; margin-bottom: 10px; color: #ccc;"><i class="fa-solid fa-image"></i> Card Image</label>
                                <img id="service-detail-img-${id}" src="${imgSrc}" style="width: 100%; height: 140px; object-fit: cover; border-radius: 8px; border: 2px solid #3a3a3a; margin-bottom: 10px;">
                                <input type="file" class="vcms-input vcms-service-image-file" name="service_${id}_image_file" accept="image/*" style="font-size: 11px;" data-preview="service-detail-img-${id}" data-service-id="${id}">
                                <small style="color: #888; display: block; margin-top: 5px;">1200 x 800 px</small>
                                <input type="hidden" name="service_${id}_image" id="service-detail-img-value-${id}" value="${self.escapeHTML(imgSrcRaw)}">
                            </div>
                            <div>
                                <div class="vcms-form-group">
                                    <label><i class="fa-solid fa-heading"></i> Service Title</label>
                                    <input type="text" class="vcms-input" name="service_${id}_title" value="${self.escapeHTML(title)}">
                                </div>
                                <div class="vcms-form-group">
                                    <label><i class="fa-solid fa-align-left"></i> Short Description</label>
                                    <textarea class="vcms-textarea" name="service_${id}_desc" rows="2">${self.escapeHTML(desc)}</textarea>
                                </div>
                                <div class="vcms-form-group" style="margin-bottom: 0;">
                                    <label><i class="fa-solid fa-link"></i> Service Link</label>
                                    <input type="text" class="vcms-input" name="service_${id}_link" value="${self.escapeHTML(link)}" placeholder="/services/ui-ux-design">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Page Content -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-file-alt"></i> Detail Page Content
                        </h4>
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-image"></i> Feature Image Path</label>
                            <input type="text" class="vcms-input" name="service_${id}_feature_image" value="${self.escapeHTML(featureImage)}" placeholder="images/service-feature.jpg">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label>Description Paragraph 1</label>
                                <textarea class="vcms-textarea" name="service_${id}_desc_1" rows="4">${self.escapeHTML(desc1)}</textarea>
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label>Description Paragraph 2</label>
                                <textarea class="vcms-textarea" name="service_${id}_desc_2" rows="4">${self.escapeHTML(desc2)}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits Section -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <h4 style="color: #ffcc01; margin: 0;"><i class="fa-solid fa-check-circle"></i> Benefits</h4>
                            <button type="button" onclick="VCMSAdmin.addServiceBenefit('${id}')" style="background: #4caf50; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                <i class="fa-solid fa-plus"></i> Add
                            </button>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 12px;">Section Title</label>
                                <input type="text" class="vcms-input" name="service_${id}_betit" value="${self.escapeHTML(betit)}" placeholder="Benefits <span>title</span>">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label style="font-size: 12px;">Section Description</label>
                                <textarea class="vcms-textarea" name="service_${id}_bedes" rows="2">${self.escapeHTML(bedes)}</textarea>
                            </div>
                        </div>
                        <div id="service-benefits-list-${id}" style="max-height: 200px; overflow-y: auto; margin-top: 15px;">
                            <input type="hidden" name="service_${id}_benefit_count" id="service-benefit-count-${id}" value="${benefitCount}">
                            ${benefitsListHTML}
                        </div>
                    </div>

                    <!-- Two Column: FAQs + Sidebar CTA -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <!-- FAQs -->
                        <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                                <h4 style="color: #ffcc01; margin: 0;"><i class="fa-solid fa-circle-question"></i> FAQs</h4>
                                <button type="button" onclick="VCMSAdmin.addServiceFaq('${id}')" style="background: #4caf50; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                                <input type="text" class="vcms-input" name="service_${id}_faq_subtitle" value="${self.escapeHTML(faqSubTitle)}" placeholder="Sub Title (e.g. FAQ's)">
                                <input type="text" class="vcms-input" name="service_${id}_fatit" value="${self.escapeHTML(fatit)}" placeholder="Main Title">
                            </div>
                            <div id="service-faq-list-${id}" style="max-height: 300px; overflow-y: auto;">
                                <input type="hidden" name="service_${id}_faq_count" id="service-faq-count-${id}" value="${faqCount}">
                                ${faqListHTML}
                            </div>
                        </div>
                        
                        <!-- Sidebar CTA -->
                        <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                            <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                                <i class="fa-solid fa-envelope"></i> Sidebar CTA
                            </h4>
                            <div class="vcms-form-group">
                                <label style="font-size: 12px;">CTA Text</label>
                                <input type="text" class="vcms-input" name="service_${id}_cta_text" value="${self.escapeHTML(ctaText)}" placeholder="We always available to discuss with you">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div class="vcms-form-group" style="margin-bottom: 0;">
                                    <label style="font-size: 12px;">Email</label>
                                    <input type="email" class="vcms-input" name="service_${id}_cta_email" value="${self.escapeHTML(ctaEmail)}" placeholder="info@domain.com">
                                </div>
                                <div class="vcms-form-group" style="margin-bottom: 0;">
                                    <label style="font-size: 12px;">Button Text</label>
                                    <input type="text" class="vcms-input" name="service_${id}_cta_btn_text" value="${self.escapeHTML(ctaBtnText)}" placeholder="contact us">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        // Generator untuk List Project (Video)
        generateProjectListForm: function (element) {
            let listHTML = "";
            const self = this;
            
            // Store projects data
            this.projectsData = [];

            // Loop setiap item (col-lg-4 atau col-md-6)
            element.find(".work-item").each(function (index) {
                const item = $(this);
                
                // Get project ID from data attribute (from database) - this is the KEY fix!
                const projectId = item.attr("data-project-id") || item.data("project-id") || (index + 1);
                
                // Get media type from data attribute (from database) - prioritize this!
                const hasIframe = item.find("iframe").length > 0;
                const hasImage = item.find(".image-anime img").length > 0;
                
                // Use data-media-type from database first, fallback to DOM detection
                let mediaType = item.attr("data-media-type") || item.data("media-type") || (hasIframe ? "video" : "image");
                let mediaSrc = "";
                
                if (hasIframe) {
                    mediaSrc = item.find("iframe").attr("src") || "";
                } else if (hasImage) {
                    mediaSrc = item.find(".image-anime img").attr("src") || "";
                }
                
                const title = item.find(".work-content h3 a").text().trim();
                const desc = item.find(".work-content p").text().trim();
                const link = item.find(".work-btn a").attr("href") || "#";
                
                // Store project data with actual project ID from database
                self.projectsData.push({ 
                    id: projectId, 
                    mediaType, 
                    mediaSrc, 
                    title, 
                    desc, 
                    link, 
                    index 
                });

                const mediaIcon = mediaType === "video" ? 
                    '<i class="fa-brands fa-youtube" style="color: #ff4444;"></i>' : 
                    '<i class="fa-solid fa-image" style="color: #4CAF50;"></i>';

                listHTML += `
                    <div class="vcms-project-item" data-project-id="${projectId}" data-index="${index}" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px; position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #3a3a3a; margin-bottom: 15px; padding-bottom: 12px;">
                            <span style="font-weight: 700; color: #ffcc01; font-size: 14px;">
                                ${mediaIcon}
                                <span style="margin-left: 8px;">Project #${index + 1}</span>
                            </span>
                            <button type="button" class="btn btn-sm vcms-delete-project-btn" data-project-id="${projectId}" data-index="${index}" onclick="VCMSAdmin.deleteProject('${projectId}', ${index}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 12px; border-radius: 6px; font-size: 12px; position: relative; z-index: 9999;">
                                <i class="fa-solid fa-trash" style="pointer-events: none;"></i> <span style="pointer-events: none;">Delete</span>
                            </button>
                        </div>

                        <div class="vcms-form-group">
                            <label>Media Type</label>
                            <select class="vcms-select vcms-project-media-type" name="project_${projectId}_media_type" data-index="${projectId}" onchange="VCMSAdmin.toggleProjectMediaInput('${projectId}')">
                                <option value="video" ${mediaType === "video" ? "selected" : ""}>Video (YouTube URL)</option>
                                <option value="image" ${mediaType === "image" ? "selected" : ""}>Image (Upload)</option>
                            </select>
                        </div>

                        <div class="vcms-project-video-input" id="project-video-input-${projectId}" style="${mediaType === "video" ? "" : "display: none;"}">
                            <div class="vcms-form-group">
                                <label>YouTube Embed URL</label>
                                <input type="text" class="vcms-input" name="project_${projectId}_video_url" value="${mediaType === "video" ? self.escapeHTML(mediaSrc) : ""}" placeholder="https://www.youtube.com/embed/...">
                                <small>Example: https://www.youtube.com/embed/VIDEO_ID</small>
                            </div>
                        </div>

                        <div class="vcms-project-image-input" id="project-image-input-${projectId}" style="${mediaType === "image" ? "" : "display: none;"}">
                            <div class="vcms-form-group">
                                <label>Project Image</label>
                                <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; margin-bottom: 10px; text-align: center;">
                                    <img id="project-img-preview-${projectId}" src="${mediaType === "image" ? mediaSrc : "images/project-dummy-1.jpg"}" style="max-height: 100px; border-radius: 8px; border: 1px solid #3a3a3a;">
                                </div>
                                <input type="hidden" name="project_${projectId}_current_image" value="${mediaType === "image" ? mediaSrc : ""}">
                                <input type="file" class="vcms-input vcms-project-image-file" name="project_${projectId}_image" accept="image/*" data-preview="project-img-preview-${projectId}">
                            </div>
                        </div>

                        <div class="vcms-form-group">
                            <label>Project Title</label>
                            <input type="text" class="vcms-input" name="project_${projectId}_title" value="${self.escapeHTML(title)}">
                        </div>

                        <div class="vcms-form-group">
                            <label>Short Description</label>
                            <input type="text" class="vcms-input" name="project_${projectId}_desc" value="${self.escapeHTML(desc)}">
                        </div>

                        <div class="vcms-form-group">
                            <label>Project Link</label>
                            <input type="text" class="vcms-input" name="project_${projectId}_link" value="${self.escapeHTML(link)}">
                        </div>
                        
                        <input type="hidden" name="project_${projectId}_exists" value="1">
                    </div>
                `;
            });

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-photo-film" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="projects-count">${element.find(".work-item").length}</span> projects</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addNewProject()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none; box-shadow: 0 4px 15px rgba(255, 204, 1, 0.3);">
                        <i class="fa-solid fa-plus"></i> Add Project
                    </button>
                </div>

                <div class="vcms-scroll-wrapper" id="projects-list-container" style="max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    ${listHTML || '<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No projects yet. Click "Add Project" to create one.</div>'}
                </div>

                <div style="margin-top: 20px; padding: 14px 18px; background: rgba(255, 152, 0, 0.1); border-left: 4px solid #ff9800; border-radius: 8px; font-size: 13px; color: #ffb74d;">
                    <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i> 
                    <strong>Note:</strong> Video uses YouTube embed URL, Image will be uploaded.
                </div>
            `;
        },
        
        // Toggle project media input (video/image)
        toggleProjectMediaInput: function(projectId) {
            const mediaType = $(`select[name="project_${projectId}_media_type"]`).val();
            const videoInput = $(`#project-video-input-${projectId}`);
            const imageInput = $(`#project-image-input-${projectId}`);
            
            if (mediaType === "video") {
                videoInput.show();
                imageInput.hide();
            } else {
                videoInput.hide();
                imageInput.show();
            }
            
            // Update icon
            const item = $(`.vcms-project-item[data-project-id="${projectId}"]`);
            const iconSpan = item.find("span:first i");
            if (mediaType === "video") {
                iconSpan.removeClass("fa-solid fa-image").addClass("fa-brands fa-youtube").css("color", "#ff4444");
            } else {
                iconSpan.removeClass("fa-brands fa-youtube").addClass("fa-solid fa-image").css("color", "#4CAF50");
            }
        },
        
        // Add new project
        addNewProject: function() {
            const container = $("#projects-list-container");
            const currentCount = container.find(".vcms-project-item").length;
            
            // Generate new unique ID using timestamp + random string to avoid collision
            const newId = "new_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
            const newIndex = currentCount;
            
            const newProjectHTML = `
                <div class="vcms-project-item" data-project-id="${newId}" data-index="${newIndex}" style="background: #252525; border: 2px solid #ffcc01; padding: 20px; border-radius: 12px; margin-bottom: 15px; position: relative; box-shadow: 0 0 20px rgba(255, 204, 1, 0.15);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #3a3a3a; margin-bottom: 15px; padding-bottom: 12px;">
                        <span style="font-weight: 700; color: #ffcc01; font-size: 14px;">
                            <i class="fa-solid fa-plus-circle" style="margin-right: 10px;"></i>
                            New Project #${newIndex + 1}
                        </span>
                        <button type="button" class="btn btn-sm vcms-delete-project-btn" data-project-id="${newId}" data-index="${newIndex}" onclick="VCMSAdmin.deleteProject('${newId}', ${newIndex}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 12px; border-radius: 6px; font-size: 12px; position: relative; z-index: 9999;">
                            <i class="fa-solid fa-trash" style="pointer-events: none;"></i> <span style="pointer-events: none;">Delete</span>
                        </button>
                    </div>

                    <div class="vcms-form-group">
                        <label>Media Type</label>
                        <select class="vcms-select vcms-project-media-type" name="project_${newId}_media_type" data-index="${newId}" onchange="VCMSAdmin.toggleProjectMediaInput('${newId}')">
                            <option value="video">Video (YouTube URL)</option>
                            <option value="image">Image (Upload)</option>
                        </select>
                    </div>

                    <div class="vcms-project-video-input" id="project-video-input-${newId}">
                        <div class="vcms-form-group">
                            <label>YouTube Embed URL</label>
                            <input type="text" class="vcms-input" name="project_${newId}_video_url" value="" placeholder="https://www.youtube.com/embed/...">
                            <small>Example: https://www.youtube.com/embed/VIDEO_ID</small>
                        </div>
                    </div>

                    <div class="vcms-project-image-input" id="project-image-input-${newId}" style="display: none;">
                        <div class="vcms-form-group">
                            <label>Project Image</label>
                            <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; margin-bottom: 10px; text-align: center;">
                                <img id="project-img-preview-${newId}" src="images/project-dummy-1.jpg" style="max-height: 100px; border-radius: 8px; border: 1px solid #3a3a3a;">
                            </div>
                            <input type="file" class="vcms-input vcms-project-image-file" name="project_${newId}_image" accept="image/*" data-preview="project-img-preview-${newId}">
                        </div>
                    </div>

                    <div class="vcms-form-group">
                        <label>Project Title</label>
                        <input type="text" class="vcms-input" name="project_${newId}_title" value="" placeholder="Enter project title">
                    </div>

                    <div class="vcms-form-group">
                        <label>Short Description</label>
                        <input type="text" class="vcms-input" name="project_${newId}_desc" value="" placeholder="Enter project description">
                    </div>

                    <div class="vcms-form-group">
                        <label>Project Link</label>
                        <input type="text" class="vcms-input" name="project_${newId}_link" value="/portfolio" placeholder="Enter link URL">
                    </div>
                    
                    <input type="hidden" name="project_${newId}_new" value="1">
                </div>
            `;
            
            container.append(newProjectHTML);
            
            // Update count
            const newCount = container.find(".vcms-project-item").length;
            $("#projects-count").text(newCount);
            
            // Scroll to new item
            container.animate({ scrollTop: container[0].scrollHeight }, 500);
            
            this.showNotification("New project added", "success");
        },
        
        // Delete project
        deleteProject: function(projectId, index) {
            if (confirm("Are you sure you want to delete this project?")) {
                // Find item by data attribute using attr() for consistency
                let $item = $(`.vcms-project-item[data-project-id="${projectId}"]`);
                
                if ($item.length === 0) {
                    // Try alternative selector
                    $item = $(`.vcms-project-item`).filter(function() {
                        return $(this).attr('data-project-id') == projectId;
                    });
                }
                
                // Check if project exists in database (has _exists hidden input or doesn't start with new_)
                const existsInDb = $(`input[name="project_${projectId}_exists"]`).length > 0 
                                   || !String(projectId).startsWith("new_");
                if (existsInDb) {
                    $(".vcms-edit-modal-body").append(
                        `<input type="hidden" name="delete_project_${projectId}" value="1">`
                    );
                }
                
                // Remove with animation
                $item.css({
                    "background": "#ffebee",
                    "border-color": "#f44336",
                    "opacity": "0.5"
                });
                
                setTimeout(() => {
                    $item.slideUp(300, function() {
                        $(this).remove();
                        const newCount = $("#projects-list-container .vcms-project-item").length;
                        $("#projects-count").text(newCount);
                    });
                }, 300);
                
                this.showNotification("Project marked for deletion", "success");
            }
        },

        // Generator untuk Header Section (Judul & Tombol) - Jaga2 kalau belum ada
        generateWorkSectionForm: function (element) {
            // ... (Gunakan kode generateWorkSectionForm dari jawaban sebelumnya) ...
            // Kalau kamu sudah copas yang di jawaban sebelumnya, tidak perlu copas ini lagi.
            // Intinya yang pakai logika clone() dan pembersih span tadi.
            const subTitle =
                element.find(".section-title h3").text().trim() || "";
            const btnText = element.find(".section-btn a").text().trim() || "";
            const btnLink = element.find(".section-btn a").attr("href") || "#";

            const mainTitleEl = element.find(".section-title h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();

            return `
                <div class="vcms-form-group">
                    <label>Sub Title</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>
                <div class="vcms-form-group">
                    <label>Main Title</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>Use &lt;span&gt;text&lt;/span&gt; for color.</small>
                </div>
                <div class="vcms-form-group">
                    <label>Button Text</label>
                    <input type="text" class="vcms-input" name="btn_text" value="${this.escapeHTML(
                        btnText
                    )}">
                </div>
                 <div class="vcms-form-group">
                    <label>Button Link</label>
                    <input type="text" class="vcms-input" name="btn_link" value="${this.escapeHTML(
                        btnLink
                    )}">
                </div>
            `;
        },
        generateCreativeToolsForm: function (element) {
            const subTitle =
                element.find(".section-title h3").text().trim() || "";
            const btnText = element.find(".section-btn a").text().trim() || "";
            const btnLink = element.find(".section-btn a").attr("href") || "#";

            // === LOGIKA PEMBERSIH ANIMASI ===
            const mainTitleEl = element.find(".section-title h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();
            // ================================

            return `
                <div class="vcms-form-group">
                    <label>Sub Title</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Main Title</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>Use &lt;span&gt;text&lt;/span&gt; for highlight color.</small>
                </div>

                <div class="vcms-form-group">
                    <label>Button Text</label>
                    <input type="text" class="vcms-input" name="btn_text" value="${this.escapeHTML(
                        btnText
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Button Link</label>
                    <input type="text" class="vcms-input" name="btn_link" value="${this.escapeHTML(
                        btnLink
                    )}">
                </div>
            `;
        },
        generateToolsListForm: function (element) {
            let listHTML = "";
            const self = this;
            let toolIndex = 0;

            // Loop setiap item tool
            element.find(".creative-tool-item").each(function (index) {
                const item = $(this);
                const toolId = item.data("tool-id") || index;
                const toolName = item.find(".creative-tool-item-content h3").text().trim();
                const toolCat = item.find(".creative-tool-item-content p").text().trim();
                let iconSrc = item.find("img").attr("src") || "images/icon-creative-tools-1.svg";

                listHTML += `
                    <div class="vcms-tool-item" data-tool-id="${toolId}" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <span style="color: #ffcc01; font-weight: 600; font-size: 14px;">
                                <i class="fa-solid fa-toolbox"></i> Tool #${index + 1}
                            </span>
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteTool('${toolId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>
                        
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <div style="width: 50px; height: 50px; background: #161616; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #3a3a3a;">
                                <img id="tool-icon-preview-${toolId}" src="${iconSrc}" style="max-width: 30px; max-height: 30px;">
                            </div>
                            <div style="flex-grow: 1;">
                                <div style="margin-bottom: 10px;">
                                    <label style="font-size: 0.75em; font-weight: 600; color: #999;">Tool Name</label>
                                    <input type="text" class="vcms-input" name="tool_${toolId}_name" value="${self.escapeHTML(toolName)}" style="margin-bottom: 5px;">
                                </div>
                                <div style="margin-bottom: 10px;">
                                    <label style="font-size: 0.75em; font-weight: 600; color: #999;">Category</label>
                                    <input type="text" class="vcms-input" name="tool_${toolId}_cat" value="${self.escapeHTML(toolCat)}">
                                </div>
                                <div>
                                    <label style="font-size: 0.75em; font-weight: 600; color: #999;">Icon (Upload)</label>
                                    <input type="file" class="vcms-input" name="tool_${toolId}_icon_file" accept="image/*" data-preview="tool-icon-preview-${toolId}" style="font-size: 12px; padding: 6px;">
                                    <input type="hidden" name="tool_${toolId}_icon_current" value="${self.escapeHTML(iconSrc)}">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                toolIndex++;
            });

            const toolCount = toolIndex;

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-tools" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="tools-count">${toolCount}</span> creative tools</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addTool()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-plus"></i> Add Tool
                    </button>
                </div>

                <div class="vcms-scroll-wrapper" id="tools-container" style="max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    ${listHTML || '<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-toolbox" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No tools yet. Click "Add Tool" to add one.</div>'}
                </div>
            `;
        },

        // Add new tool
        addTool: function () {
            const container = $("#tools-container");
            const currentCount = container.find(".vcms-tool-item").length;
            
            let maxKey = -1;
            container.find(".vcms-tool-item").each(function() {
                const key = $(this).data("tool-id");
                const numKey = parseInt(key, 10);
                if (!isNaN(numKey) && numKey > maxKey) {
                    maxKey = numKey;
                }
            });
            const newId = maxKey + 1;
            const defaultIcon = "images/icon-creative-tools-1.svg";
            
            const newToolHTML = `
                <div class="vcms-tool-item" data-tool-id="${newId}" style="background: #1a3a1a; border: 1px solid #28a745; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="color: #28a745; font-weight: 600; font-size: 14px;">
                            <i class="fa-solid fa-toolbox"></i> Tool #${currentCount + 1} (Baru)
                        </span>
                        <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteTool('${newId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>
                    
                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                        <div style="width: 50px; height: 50px; background: #161616; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #3a3a3a;">
                            <img id="tool-icon-preview-${newId}" src="${defaultIcon}" style="max-width: 30px; max-height: 30px;">
                        </div>
                        <div style="flex-grow: 1;">
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 0.75em; font-weight: 600; color: #999;">Tool Name</label>
                                <input type="text" class="vcms-input" name="tool_${newId}_name" value="" placeholder="e.g. Figma" style="margin-bottom: 5px;">
                            </div>
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 0.75em; font-weight: 600; color: #999;">Category</label>
                                <input type="text" class="vcms-input" name="tool_${newId}_cat" value="" placeholder="e.g. Design tool">
                            </div>
                            <div>
                                <label style="font-size: 0.75em; font-weight: 600; color: #999;">Icon (Upload)</label>
                                <input type="file" class="vcms-input" name="tool_${newId}_icon_file" accept="image/*" data-preview="tool-icon-preview-${newId}" style="font-size: 12px; padding: 6px;">
                                <input type="hidden" name="tool_${newId}_icon_current" value="${defaultIcon}">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.find('div[style*="text-align: center"]').remove();
            container.append(newToolHTML);
            $("#tools-count").text(currentCount + 1);
            container.animate({ scrollTop: container[0].scrollHeight }, 300);
        },

        // Delete tool
        deleteTool: function (toolId) {
            if (confirm("Apakah Anda yakin ingin menghapus tool ini?")) {
                const container = $("#tools-container");
                container.find(`.vcms-tool-item[data-tool-id="${toolId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    const newCount = container.find(".vcms-tool-item").length;
                    $("#tools-count").text(newCount);
                    if (newCount === 0) {
                        container.html('<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-toolbox" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No tools yet. Click "Add Tool" to add one.</div>');
                    }
                });
                // Add hidden input to mark for deletion
                const deleteInput = `<input type="hidden" name="delete_tool_${toolId}" value="1">`;
                $(".vcms-edit-modal-body").append(deleteInput);
            }
        },
        // Generator untuk Judul Section "How We Work"
        generateHowItWorksForm: function (element) {
            const subTitle =
                element.find(".section-title h3").text().trim() || "";

            // Logika pembersih span (agar animasi tidak rusak)
            const mainTitleEl = element.find(".section-title h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();

            return `
                <div class="vcms-form-group">
                    <label>Sub Title</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Main Title</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>Use &lt;span&gt;text&lt;/span&gt; for color.</small>
                </div>
                
                <div class="alert alert-info" style="margin-top: 15px; padding: 10px; background: #e3f2fd; border-radius: 4px; font-size: 0.9em;">
                    <i class="fa-solid fa-image"></i> 
                    <strong>Note:</strong> To change the main image or the "Contact Now" circle, please use the small edit icons directly on those images.
                </div>
            `;
        },

        // Generator untuk Accordion List (Langkah-langkah)
        generateProcessListForm: function (element) {
            let listHTML = "";
            const self = this;
            let processIndex = 0;

            // Loop setiap item accordion
            element.find(".work-accordion-item").each(function (index) {
                const item = $(this);
                const processId = item.data("process-id") || index;
                const title = item.find(".accordion-button").text().trim();
                const desc = item.find(".accordion-body p").text().trim();

                listHTML += `
                    <div class="vcms-process-item" data-process-id="${processId}" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #3a3a3a; padding-bottom: 10px;">
                            <span style="font-weight: 700; color: #ffcc01;">
                                <i class="fa-solid fa-list-ol"></i> Step #${index + 1}
                            </span>
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteProcess('${processId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>

                        <div class="vcms-form-group">
                            <label>Step Title</label>
                            <input type="text" class="vcms-input" name="step_${processId}_title" value="${self.escapeHTML(title)}">
                        </div>

                        <div class="vcms-form-group" style="margin-bottom: 0;">
                            <label>Description</label>
                            <textarea class="vcms-textarea" name="step_${processId}_desc" rows="3">${self.escapeHTML(desc)}</textarea>
                        </div>
                    </div>
                `;
                processIndex++;
            });

            const processCount = processIndex;

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-list-ol" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="process-count">${processCount}</span> steps</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addProcess()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-plus"></i> Add Step
                    </button>
                </div>

                <div class="vcms-scroll-wrapper" id="process-container" style="max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    ${listHTML || '<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-list-ol" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No steps yet. Click "Add Step" to add one.</div>'}
                </div>
            `;
        },

        // Add new process step
        addProcess: function () {
            const container = $("#process-container");
            const currentCount = container.find(".vcms-process-item").length;
            
            let maxKey = -1;
            container.find(".vcms-process-item").each(function() {
                const key = $(this).data("process-id");
                const numKey = parseInt(key, 10);
                if (!isNaN(numKey) && numKey > maxKey) {
                    maxKey = numKey;
                }
            });
            const newId = maxKey + 1;
            
            const newProcessHTML = `
                <div class="vcms-process-item" data-process-id="${newId}" style="background: #1a3a1a; border: 1px solid #28a745; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #28a745; padding-bottom: 10px;">
                        <span style="font-weight: 700; color: #28a745;">
                            <i class="fa-solid fa-list-ol"></i> Step #${currentCount + 1} (Baru)
                        </span>
                        <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteProcess('${newId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>

                    <div class="vcms-form-group">
                        <label>Step Title</label>
                        <input type="text" class="vcms-input" name="step_${newId}_title" value="" placeholder="e.g. Discovery & Strategy">
                    </div>

                    <div class="vcms-form-group" style="margin-bottom: 0;">
                        <label>Description</label>
                        <textarea class="vcms-textarea" name="step_${newId}_desc" rows="3" placeholder="Describe this step..."></textarea>
                    </div>
                </div>
            `;
            
            container.find('div[style*="text-align: center"]').remove();
            container.append(newProcessHTML);
            $("#process-count").text(currentCount + 1);
            container.animate({ scrollTop: container[0].scrollHeight }, 300);
        },

        // Delete process step
        deleteProcess: function (processId) {
            if (confirm("Apakah Anda yakin ingin menghapus step ini?")) {
                const container = $("#process-container");
                container.find(`.vcms-process-item[data-process-id="${processId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    const newCount = container.find(".vcms-process-item").length;
                    $("#process-count").text(newCount);
                    if (newCount === 0) {
                        container.html('<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-list-ol" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No steps yet. Click "Add Step" to add one.</div>');
                    }
                });
                // Add hidden input to mark for deletion
                const deleteInput = `<input type="hidden" name="delete_process_${processId}" value="1">`;
                $(".vcms-edit-modal-body").append(deleteInput);
            }
        },
        generateImageWithLinkForm: function (element) {
            // Ambil data gambar dan link saat ini
            const imgPath = element.find("img").attr("src") || "";
            const linkUrl = element.find("a").attr("href") || "#";
            
            // Determine default image based on element context
            const defaultImage = "images/contact-circle-yellow.png";

            return `
                <div class="vcms-form-group">
                    <label>Current Image Preview</label>
                    <div class="vcms-current-image-preview" style="background: #161616; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 10px; border: 1px solid #3a3a3a;">
                        <img id="vcms-image-preview-main" src="${imgPath}" style="max-height: 120px; max-width: 100%; border-radius: 8px;">
                        <input type="hidden" name="current_image_path" value="${imgPath}">
                        <input type="hidden" name="default_image_path" value="${defaultImage}">
                    </div>
                    <button type="button" class="btn btn-sm" onclick="VCMSAdmin.resetToDefault('vcms-image-preview-main', '${defaultImage}', 'image_file')" style="margin-bottom: 10px; background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px;">
                        <i class="fa-solid fa-undo"></i> Reset to Default
                    </button>
                </div>

                <div class="vcms-form-group">
                    <label>Upload New Image (Replace)</label>
                    <div class="vcms-file-upload-wrapper" style="border: 2px dashed #3a3a3a; border-radius: 12px; padding: 30px; text-align: center; cursor: pointer; transition: 0.3s; background: #161616;" id="vcms-dropzone">
                        <input type="file" class="vcms-input vcms-file-input" name="image_file" accept="image/*" id="vcms-file-input-main" style="display: none;">
                        <label for="vcms-file-input-main" style="cursor: pointer; margin: 0;">
                            <i class="fa-solid fa-cloud-upload-alt" style="font-size: 32px; color: #ffcc01; margin-bottom: 12px; display: block;"></i>
                            <span style="color: #e0e0e0;">Click to upload or drag & drop</span>
                            <small style="display: block; color: #666; margin-top: 8px;">PNG, JPG, GIF up to 10MB</small>
                        </label>
                    </div>
                    <div id="vcms-new-image-preview" style="display: none; margin-top: 15px; text-align: center; background: #252525; padding: 15px; border-radius: 8px; border: 1px solid #3a3a3a;">
                        <p style="color: #4CAF50; font-size: 0.9em;"><i class="fa-solid fa-check"></i> New image selected:</p>
                        <img id="vcms-new-preview-img" style="max-height: 80px; border-radius: 6px;">
                        <button type="button" id="vcms-remove-new-image" style="display: block; margin: 12px auto 0; background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 6px 16px; border-radius: 6px; cursor: pointer; font-size: 0.8em;">
                            <i class="fa-solid fa-times"></i> Remove
                        </button>
                    </div>
                </div>

                <div class="vcms-form-group">
                    <label>Link URL</label>
                    <input type="text" class="vcms-input" name="link_url" value="${this.escapeHTML(linkUrl)}">
                    <small>URL to navigate when image is clicked</small>
                </div>
            `;
        },
        generateHowImagesForm: function (element) {
            // Ambil 2 gambar yang ada di section ini
            const circleImg = element.find(".how-work-image-circle img").attr("src") || "";
            const mainImg = element.find(".how-it-work-image img").attr("src") || "";
            
            // Default images
            const defaultMainImg = "images/how-it-work-image.jpg";
            const defaultCircleImg = "images/how-work-image-circle.png";

            return `
                <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                    <h5 style="margin-top: 0; margin-bottom: 15px; font-size: 1rem; color: #ffcc01; font-weight: 700;">1. Main Image (Circle)</h5>
                    
                    <div class="vcms-form-group">
                        <label>Current Image:</label>
                        <div style="margin-bottom: 12px; background: #161616; padding: 15px; border-radius: 10px; text-align: center; border: 1px solid #3a3a3a;">
                            <img id="main-img-preview" src="${mainImg}" style="max-height: 100px; border-radius: 8px; border: 1px solid #3a3a3a;">
                            <input type="hidden" name="current_main_image" value="${mainImg}">
                            <input type="hidden" name="default_main_image" value="${defaultMainImg}">
                        </div>
                        <button type="button" class="btn btn-sm vcms-reset-btn" onclick="VCMSAdmin.resetToDefault('main-img-preview', '${defaultMainImg}', 'main_image')" style="margin-bottom: 12px; background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px;">
                            <i class="fa-solid fa-undo"></i> Reset to Default
                        </button>
                        <label>Upload New Image:</label>
                        <input type="file" class="vcms-input vcms-file-input" name="main_image" accept="image/*" data-preview="main-preview">
                        <div id="main-preview-container" style="display: none; margin-top: 12px; text-align: center; background: #161616; padding: 12px; border-radius: 8px; border: 1px solid #3a3a3a;">
                            <span style="color: #4CAF50; font-size: 0.8em;"><i class="fa-solid fa-check"></i> New image:</span>
                            <img id="main-preview" style="max-height: 60px; margin-top: 8px; border-radius: 6px;">
                        </div>
                    </div>
                </div>

                <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <h5 style="margin-top: 0; margin-bottom: 15px; font-size: 1rem; color: #ffcc01; font-weight: 700;">2. Decorative Circle Image</h5>
                    
                    <div class="vcms-form-group">
                        <label>Current Image:</label>
                        <div style="margin-bottom: 12px; background: #161616; padding: 15px; border-radius: 10px; text-align: center; border: 1px solid #3a3a3a;">
                            <img id="circle-img-preview" src="${circleImg}" style="max-height: 80px;">
                            <input type="hidden" name="current_circle_image" value="${circleImg}">
                            <input type="hidden" name="default_circle_image" value="${defaultCircleImg}">
                        </div>
                        <button type="button" class="btn btn-sm vcms-reset-btn" onclick="VCMSAdmin.resetToDefault('circle-img-preview', '${defaultCircleImg}', 'circle_deco')" style="margin-bottom: 12px; background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px;">
                            <i class="fa-solid fa-undo"></i> Reset to Default
                        </button>
                        <label>Upload New Image:</label>
                        <input type="file" class="vcms-input vcms-file-input" name="circle_deco" accept="image/*" data-preview="circle-preview">
                        <div id="circle-preview-container" style="display: none; margin-top: 12px; text-align: center; background: #161616; padding: 12px; border-radius: 8px; border: 1px solid #3a3a3a;">
                            <span style="color: #4CAF50; font-size: 0.8em;"><i class="fa-solid fa-check"></i> New image:</span>
                            <img id="circle-preview" style="max-height: 60px; margin-top: 8px;">
                        </div>
                        <small>Recommended: Transparent PNG</small>
                    </div>
                </div>
            `;
        },
        // Generator untuk Header Kiri (Judul, Span, Paragraf)
        generateAchievementsHeaderForm: function (element) {
            // 1. Logika untuk mendapatkan container Title/Desc (Ini sudah benar)
            // Jika yang diklik tombol "Edit Achievements" (wrapper luar), kita cari .section-title di dalamnya
            const container = element.hasClass("section-title")
                ? element
                : element.find(".section-title");

            const subTitle = container.find("h3").text().trim() || "";
            const desc = container.find("p").text().trim() || "";

            // PERBAIKAN: Ambil parent dari container (yaitu .our-achievements-content)
            const contentWrapper = container.parent();

            // MENGAMBIL DATA BUTTON: Cari tombol di dalam contentWrapper
            // Gunakan contentWrapper (bukan lagi element.closest() yang rentan error)
            const buttonContainer = contentWrapper.find(
                ".achievements-content-btn"
            );
            const buttonElement = buttonContainer.find("a.btn-default");

            // DATA BUTTON SEKARANG AKAN TERISI
            const btnText = buttonElement.text().trim() || "";
            const btnLink = buttonElement.attr("href") || "";

            // Logika pembersih span (sama seperti sebelumnya)
            const mainTitleEl = container.find("h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();

            return `
        <div class="vcms-form-group">
            <label>Sub Title</label>
            <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                subTitle
            )}">
        </div>

        <div class="vcms-form-group">
            <label>Main Title</label>
            <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                cleanMainTitle
            )}">
            <small>Use &lt;span&gt;text&lt;/span&gt; for color.</small>
        </div>

        <div class="vcms-form-group">
            <label>Description Paragraph</label>
            <textarea class="vcms-textarea" name="description" rows="4">${this.escapeHTML(
                desc
            )}</textarea>
        </div>

        <hr class="vcms-divider">
        <label class="vcms-label-group-title">Call to Action Button</label>

        <div class="vcms-form-group">
            <label>Button Text</label>
            <input type="text" class="vcms-input" name="btn_text" value="${this.escapeHTML(
                btnText
            )}">
        </div>

        <div class="vcms-form-group">
            <label>Button Link/URL</label>
            <input type="text" class="vcms-input" name="btn_link" value="${this.escapeHTML(
                btnLink
            )}">
        </div>
    `;
        },

        // Generator untuk List Penghargaan (Tahun, Judul, Deskripsi)
        generateAchievementListForm: function (element) {
            let listHTML = "";
            const self = this;
            let achievementIndex = 0;

            element.find(".achievements-item").each(function (index) {
                const item = $(this);
                const achievementId = item.data("achievement-id") || index;
                const year = item.find("h3").text().trim();
                const awardName = item.find("h2").text().trim();
                const desc = item.find("p").text().trim();

                listHTML += `
                    <div class="vcms-achievement-item" data-achievement-id="${achievementId}" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #3a3a3a; padding-bottom: 10px;">
                            <span style="font-weight: 700; color: #ffcc01;">
                                <i class="fa-solid fa-trophy"></i> Achievement #${index + 1}
                            </span>
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteAchievement('${achievementId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>

                        <div class="row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                            <div class="vcms-form-group" style="flex: 1; margin-bottom: 0;">
                                <label>Year Range</label>
                                <input type="text" class="vcms-input" name="ach_${achievementId}_year" value="${self.escapeHTML(year)}" placeholder="e.g. 2020 - 2021">
                            </div>
                            <div class="vcms-form-group" style="flex: 2; margin-bottom: 0;">
                                <label>Award Name</label>
                                <input type="text" class="vcms-input" name="ach_${achievementId}_name" value="${self.escapeHTML(awardName)}">
                            </div>
                        </div>

                        <div class="vcms-form-group" style="margin-bottom: 0;">
                            <label>Short Description</label>
                            <textarea class="vcms-textarea" name="ach_${achievementId}_desc" rows="2" style="min-height: 60px;">${self.escapeHTML(desc)}</textarea>
                        </div>
                    </div>
                `;
                achievementIndex++;
            });

            const achievementCount = achievementIndex;

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-trophy" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="achievement-count">${achievementCount}</span> achievements</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addAchievement()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-plus"></i> Add Achievement
                    </button>
                </div>

                <div class="vcms-scroll-wrapper" id="achievement-container" style="max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    ${listHTML || '<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-trophy" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No achievements yet. Click "Add Achievement" to add one.</div>'}
                </div>
            `;
        },

        // Add new achievement
        addAchievement: function () {
            const container = $("#achievement-container");
            const currentCount = container.find(".vcms-achievement-item").length;
            
            let maxKey = -1;
            container.find(".vcms-achievement-item").each(function() {
                const key = $(this).data("achievement-id");
                const numKey = parseInt(key, 10);
                if (!isNaN(numKey) && numKey > maxKey) {
                    maxKey = numKey;
                }
            });
            const newId = maxKey + 1;
            
            const newAchievementHTML = `
                <div class="vcms-achievement-item" data-achievement-id="${newId}" style="background: #1a3a1a; border: 1px solid #28a745; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #28a745; padding-bottom: 10px;">
                        <span style="font-weight: 700; color: #28a745;">
                            <i class="fa-solid fa-trophy"></i> Achievement #${currentCount + 1} (Baru)
                        </span>
                        <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteAchievement('${newId}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>

                    <div class="row" style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div class="vcms-form-group" style="flex: 1; margin-bottom: 0;">
                            <label>Year Range</label>
                            <input type="text" class="vcms-input" name="ach_${newId}_year" value="" placeholder="e.g. 2020 - 2021">
                        </div>
                        <div class="vcms-form-group" style="flex: 2; margin-bottom: 0;">
                            <label>Award Name</label>
                            <input type="text" class="vcms-input" name="ach_${newId}_name" value="" placeholder="e.g. Best Design Award">
                        </div>
                    </div>

                    <div class="vcms-form-group" style="margin-bottom: 0;">
                        <label>Short Description</label>
                        <textarea class="vcms-textarea" name="ach_${newId}_desc" rows="2" style="min-height: 60px;" placeholder="Describe this achievement..."></textarea>
                    </div>
                </div>
            `;
            
            container.find('div[style*="text-align: center"]').remove();
            container.append(newAchievementHTML);
            $("#achievement-count").text(currentCount + 1);
            container.animate({ scrollTop: container[0].scrollHeight }, 300);
        },

        // Delete achievement
        deleteAchievement: function (achievementId) {
            if (confirm("Apakah Anda yakin ingin menghapus achievement ini?")) {
                const container = $("#achievement-container");
                container.find(`.vcms-achievement-item[data-achievement-id="${achievementId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    const newCount = container.find(".vcms-achievement-item").length;
                    $("#achievement-count").text(newCount);
                    if (newCount === 0) {
                        container.html('<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-trophy" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No achievements yet. Click "Add Achievement" to add one.</div>');
                    }
                });
                // Add hidden input to mark for deletion
                const deleteInput = `<input type="hidden" name="delete_achievement_${achievementId}" value="1">`;
                $(".vcms-edit-modal-body").append(deleteInput);
            }
        },
        generateRichTextForm: function (element) {
            const textContent = element.find("p").text().trim() || "";

            return `
                <div class="vcms-form-group">
                    <label>Description Content</label>
                    <textarea class="vcms-textarea" name="text_content" rows="6">${this.escapeHTML(
                        textContent
                    )}</textarea>
                </div>
            `;
        },
        generateCounterListForm: function (element) {
            let listHTML = "";
            const self = this;

            element.find(".cta-counter-item").each(function (index) {
                const item = $(this);

                // Ambil Angka (di dalam span class="counter")
                const number = item.find(".counter").text().trim();

                // Ambil Simbol (+ atau %)
                // Caranya: ambil text dari H3, lalu buang angkanya
                const fullH3 = item.find("h3").text().trim(); // misal "45+"
                const symbol = fullH3.replace(number, "").trim(); // sisa "+"

                // Ambil Label
                const label = item.find("p").text().trim();

                listHTML += `
                    <div class="vcms-list-item-box" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px; display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label>Number (Animated)</label>
                            <input type="number" class="vcms-input" name="cnt_${index}_num" value="${self.escapeHTML(
                    number
                )}" placeholder="e.g. 45">
                        </div>
                        <div style="flex: 0 0 80px;">
                            <label>Symbol</label>
                            <input type="text" class="vcms-input" name="cnt_${index}_sym" value="${self.escapeHTML(
                    symbol
                )}" placeholder="+ or %">
                        </div>
                        <div style="flex: 2;">
                            <label>Label</label>
                            <input type="text" class="vcms-input" name="cnt_${index}_label" value="${self.escapeHTML(
                    label
                )}" placeholder="e.g. Projects">
                        </div>
                    </div>
                `;
            });

            return `
                <div style="background: #252525; border: 1px solid #3a3a3a; padding: 16px; border-radius: 12px; margin-bottom: 20px; color: #e0e0e0; font-size: 14px;">
                    <i class="fa-solid fa-calculator" style="color: #ffcc01; margin-right: 8px;"></i>
                    Editing <strong style="color: #ffcc01;">${
                        element.find(".cta-counter-item").length
                    } counters</strong>.
                </div>
                <div class="vcms-scroll-wrapper">
                    ${listHTML}
                </div>
            `;
        },
        generateTeamSectionForm: function (element) {
            const subTitle =
                element.find(".section-title h3").text().trim() || "";
            const btnText = element.find(".section-btn a").text().trim() || "";
            const btnLink = element.find(".section-btn a").attr("href") || "#";

            // === LOGIKA PEMBERSIH ANIMASI ===
            const mainTitleEl = element.find(".section-title h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();
            // ================================

            return `
                <div class="vcms-form-group">
                    <label>Sub Title</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Main Title</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>Use &lt;span&gt;text&lt;/span&gt; for highlight color.</small>
                </div>

                <div class="vcms-form-group">
                    <label>Button Text</label>
                    <input type="text" class="vcms-input" name="btn_text" value="${this.escapeHTML(
                        btnText
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Button Link</label>
                    <input type="text" class="vcms-input" name="btn_link" value="${this.escapeHTML(
                        btnLink
                    )}">
                </div>
            `;
        },
        generateTeamListForm: function (element) {
            let listHTML = "";
            const self = this;
            
            // Default team images
            const defaultTeamImages = [
                'images/team-1.jpg',
                'images/team-2.jpg',
                'images/team-3.jpg',
                'images/team-4.jpg',
                'images/team-5.jpg',
                'images/team-6.jpg',
                'images/team-7.jpg',
                'images/team-8.jpg'
            ];

            // Loop setiap item team
            element.find(".team-item").each(function (index) {
                const item = $(this);
                const name = item.find(".team-content h3").text().trim();
                const role = item.find(".team-social-list span").first().text().trim();
                const imgSrc = item.find(".team-image img").attr("src") || "";
                const defaultImg = defaultTeamImages[index] || defaultTeamImages[0];

                // Ambil data dari data attributes (tersimpan di database)
                const experience = item.data("experience") || "";
                const about = item.data("about") || "";
                const skillsDesc = item.data("skills-desc") || "";
                const expertiseDesc = item.data("expertise-desc") || "";
                const expertiseCount = parseInt(item.data("expertise-count")) || 4;
                const skillsCount = parseInt(item.data("skills-count")) || 1;
                
                // Ambil expertise items dari data attributes
                let expertiseItems = [];
                for (let e = 1; e <= expertiseCount; e++) {
                    expertiseItems[e] = item.data("expertise-" + e) || "";
                }
                
                // Ambil skills items dari data attributes
                let skillItems = [];
                for (let s = 1; s <= skillsCount; s++) {
                    skillItems[s] = {
                        title: item.data("skill-" + s + "-title") || "",
                        percent: item.data("skill-" + s + "-percent") || "80"
                    };
                }
                
                // Build expertise list HTML
                let expertiseListHTML = '';
                for (let e = 1; e <= expertiseCount; e++) {
                    const expValue = expertiseItems[e] || '';
                    const marginStyle = e === expertiseCount ? 'margin-bottom: 0;' : 'margin-bottom: 8px;';
                    expertiseListHTML += `
                                    <div class="expertise-item" data-index="${e}" style="display: flex; gap: 8px; ${marginStyle}">
                                        <input type="text" class="vcms-input" name="member_${index}_expertise_${e}" value="${self.escapeHTML(expValue)}" placeholder="Expertise ${e}" style="flex: 1; margin: 0;">
                                        <button type="button" onclick="VCMSAdmin.deleteExpertise(${index}, ${e})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                                    </div>`;
                }
                
                // Build skills list HTML
                let skillsListHTML = '';
                for (let s = 1; s <= skillsCount; s++) {
                    const skillTitle = skillItems[s].title || '';
                    const skillPercent = skillItems[s].percent || '80';
                    const marginStyle = s === skillsCount ? 'margin-bottom: 0;' : 'margin-bottom: 8px;';
                    skillsListHTML += `
                                    <div class="skill-item" data-index="${s}" style="display: flex; gap: 8px; ${marginStyle} align-items: center;">
                                        <input type="text" class="vcms-input" name="member_${index}_skill_${s}_title" value="${self.escapeHTML(skillTitle)}" placeholder="Skill name" style="flex: 2; margin: 0;">
                                        <input type="number" class="vcms-input vcms-percent-input" name="member_${index}_skill_${s}_percent" value="${self.escapeHTML(String(skillPercent))}" min="0" max="100" placeholder="%" style="flex: 1; margin: 0;" onchange="VCMSAdmin.clampPercent(this)">
                                        <span style="color: #888;">%</span>
                                        <button type="button" onclick="VCMSAdmin.deleteSkill(${index}, ${s})" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 0 10px; border-radius: 4px; cursor: pointer;"><i class="fa-solid fa-times"></i></button>
                                    </div>`;
                }

                listHTML += `
                    <div class="vcms-team-item" data-index="${index}" style="background: #252525; border: 1px solid #3a3a3a; padding: 25px; border-radius: 12px; margin-bottom: 25px;">
                        
                        <!-- Header -->
                        <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; border-bottom: 2px solid #ffcc01; padding-bottom: 15px; justify-content: space-between;">
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <img id="team-img-${index}" src="${imgSrc}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid #ffcc01;">
                                <div>
                                    <div style="font-weight: 700; color: #ffcc01; font-size: 18px;">Member #${index + 1}</div>
                                    <div style="color: #e0e0e0; font-size: 14px;">${name}</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteTeamMember(${index}); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 8px 16px; border-radius: 6px; position: relative; z-index: 9999;">
                                <i class="fa-solid fa-trash" style="pointer-events: none;"></i> Delete
                            </button>
                        </div>

                        <!-- Two Column Layout -->
                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 25px; margin-bottom: 20px;">
                            
                            <!-- Left: Photo -->
                            <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; text-align: center;">
                                <img id="team-img-preview-${index}" src="${imgSrc}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px; border: 2px solid #3a3a3a; margin-bottom: 10px;">
                                <input type="hidden" name="current_member_${index}_img" value="${imgSrc}">
                                <input type="hidden" name="default_member_${index}_img" value="${defaultImg}">
                                <input type="file" class="vcms-input" name="member_${index}_img" accept="image/*" style="font-size: 12px;">
                                <button type="button" class="btn btn-sm" onclick="VCMSAdmin.resetTeamImage(${index}, '${defaultImg}')" style="margin-top: 8px; background: rgba(255, 152, 0, 0.2); color: #ff9800; border: 1px solid #ff9800; padding: 6px 12px; border-radius: 6px; width: 100%;">
                                    <i class="fa-solid fa-undo"></i> Reset
                                </button>
                            </div>
                            
                            <!-- Right: Basic Info -->
                            <div>
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    <div class="vcms-form-group">
                                        <label>Full Name</label>
                                        <input type="text" class="vcms-input" name="member_${index}_name" value="${self.escapeHTML(name)}">
                                    </div>
                                    <div class="vcms-form-group">
                                        <label>Position / Role</label>
                                        <input type="text" class="vcms-input" name="member_${index}_role" value="${self.escapeHTML(role)}">
                                    </div>
                                    <div class="vcms-form-group">
                                        <label>Experience</label>
                                        <input type="text" class="vcms-input" name="member_${index}_experience" value="${self.escapeHTML(experience)}" placeholder="e.g., 5 years">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- About Me -->
                        <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a; margin-bottom: 20px;">
                            <label style="font-weight: 600; display: block; margin-bottom: 12px; color: #ffcc01;"><i class="fa-solid fa-user"></i> About Me</label>
                            <textarea class="vcms-textarea" name="member_${index}_about" rows="3" placeholder="Write a brief description about this team member...">${self.escapeHTML(about)}</textarea>
                        </div>

                        <!-- Two Column: Skills & Expertise -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            
                            <!-- My Skills -->
                            <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <label style="font-weight: 600; color: #ffcc01; margin: 0;"><i class="fa-solid fa-chart-bar"></i> My Skills</label>
                                    <button type="button" onclick="VCMSAdmin.addSkill(${index})" style="background: #4caf50; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="vcms-form-group">
                                    <label style="font-size: 12px;">Skills Description</label>
                                    <textarea class="vcms-textarea" name="member_${index}_skills_desc" rows="2" placeholder="Brief description of skills...">${self.escapeHTML(skillsDesc)}</textarea>
                                </div>
                                <div id="skills-list-${index}" style="margin-top: 10px;">
                                    <input type="hidden" name="member_${index}_skills_count" id="skills-count-${index}" value="${skillsCount}">
                                    ${skillsListHTML}
                                </div>
                            </div>
                            
                            <!-- My Expertise -->
                            <div style="background: #161616; padding: 15px; border-radius: 10px; border: 1px solid #3a3a3a;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <label style="font-weight: 600; color: #ffcc01; margin: 0;"><i class="fa-solid fa-star"></i> My Expertise</label>
                                    <button type="button" onclick="VCMSAdmin.addExpertise(${index})" style="background: #4caf50; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <div class="vcms-form-group">
                                    <label style="font-size: 12px;">Expertise Description</label>
                                    <textarea class="vcms-textarea" name="member_${index}_expertise_desc" rows="2" placeholder="Brief description of expertise...">${self.escapeHTML(expertiseDesc)}</textarea>
                                </div>
                                <div id="expertise-list-${index}" style="margin-top: 10px;">
                                    <input type="hidden" name="member_${index}_expertise_count" id="expertise-count-${index}" value="${expertiseCount}">
                                    ${expertiseListHTML}
                                </div>
                            </div>
                        </div>

                    </div>
                `;
            });

            const teamCount = element.find(".team-item").length;
            
            return `
                <div style="display: flex; justify-content: space-between; align-items: center; background: #252525; border: 1px solid #3a3a3a; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-users" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="team-count">${teamCount}</span> team members</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addTeamMember()" style="background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%); color: #fff; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                        <i class="fa-solid fa-plus"></i> Add Member
                    </button>
                </div>

                <input type="hidden" name="team_member_count" id="team-member-count" value="${teamCount}">

                <div class="vcms-scroll-wrapper" id="team-list-container" style="max-height: 70vh; overflow-y: auto; padding-right: 10px;">
                    ${listHTML}
                </div>
            `;
        },
        // Generator untuk Header Section Testimonials
        generateTestimonialHeaderForm: function (element) {
            // Kita cari elemen section-title karena itulah target kita
            const container = element.find(".section-title");

            const subTitle = container.find("h3").text().trim() || "";

            // === LOGIKA PEMBERSIH ANIMASI SPAN ===
            const mainTitleEl = container.find("h2").clone();
            const spans = [];
            mainTitleEl.find("span").each(function (index) {
                spans.push($(this).text());
                $(this).replaceWith(`###SPAN${index}###`);
            });
            let cleanMainTitle = mainTitleEl.text();
            spans.forEach((text, index) => {
                cleanMainTitle = cleanMainTitle.replace(
                    `###SPAN${index}###`,
                    `<span>${text}</span>`
                );
            });
            cleanMainTitle = cleanMainTitle.replace(/\s+/g, " ").trim();
            // ======================================

            return `
                <div class="vcms-form-group">
                    <label>Sub Title (Testimonials)</label>
                    <input type="text" class="vcms-input" name="sub_title" value="${this.escapeHTML(
                        subTitle
                    )}">
                </div>

                <div class="vcms-form-group">
                    <label>Main Title</label>
                    <input type="text" class="vcms-input" name="main_title" value="${this.escapeHTML(
                        cleanMainTitle
                    )}">
                    <small>Use &lt;span&gt;text&lt;/span&gt; for highlight color.</small>
                </div>

                <div class="alert alert-info" style="margin-top: 15px;">
                    <i class="fa-solid fa-info-circle"></i> 
                    To edit the actual reviews, please close this popup and click the <i class="fa-solid fa-comments"></i> icon above the slider.
                </div>
            `;
        },

        // Generator untuk Client Logos Slider
        generateClientLogosForm: function (element) {
            let listHTML = "";
            const self = this;
            
            // Store client logos data
            this.clientLogosData = [];
            let logoIndex = 0;

            // Find all logos in the slider - use numeric index
            element.find(".trusted-client-logo img").each(function () {
                const imgSrc = $(this).attr("src") || "";
                const imgAlt = $(this).attr("alt") || "Client Logo";
                // Use data-logo-key if available (from database), otherwise use current index
                const logoKey = $(this).data("logo-key") !== undefined ? $(this).data("logo-key") : logoIndex;
                const fromDb = $(this).data("from-db") === 1 || $(this).data("from-db") === "1";
                
                self.clientLogosData.push({ key: logoKey, src: imgSrc, alt: imgAlt, fromDb: fromDb });

                listHTML += `
                    <div class="vcms-client-logo-item" data-logo-key="${logoKey}" data-from-db="${fromDb ? '1' : '0'}" style="background: #252525; border: 1px solid #3a3a3a; padding: 15px; border-radius: 12px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px;">
                        <div style="width: 80px; height: 50px; background: #161616; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #3a3a3a; flex-shrink: 0;">
                            <img id="client-logo-preview-${logoKey}" src="${imgSrc}" style="max-width: 70px; max-height: 40px; object-fit: contain;">
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: #ffcc01; font-size: 13px;">Logo #${logoIndex + 1}</span>
                                <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteClientLogo('${logoKey}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                                    <i class="fa-solid fa-trash" style="pointer-events: none;"></i>
                                </button>
                            </div>
                            <input type="file" class="vcms-input vcms-client-logo-file" name="client_logo_${logoKey}" accept="image/*" data-preview="client-logo-preview-${logoKey}" style="font-size: 12px; padding: 6px;">
                            <input type="hidden" name="client_logo_${logoKey}_current" value="${self.escapeHTML(imgSrc)}">
                        </div>
                    </div>
                `;
                logoIndex++;
            });

            const logoCount = logoIndex;

            return `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 16px; background: #252525; border-radius: 12px; border: 1px solid #3a3a3a;">
                    <div style="color: #e0e0e0; font-size: 14px;">
                        <i class="fa-solid fa-images" style="color: #ffcc01; margin-right: 8px;"></i>
                        Editing <strong style="color: #ffcc01;"><span id="client-logos-count">${logoCount}</span> client logos</strong>
                    </div>
                    <button type="button" class="btn" onclick="VCMSAdmin.addClientLogo()" style="background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 700; padding: 10px 20px; border-radius: 8px; border: none; box-shadow: 0 4px 15px rgba(255, 204, 1, 0.3);">
                        <i class="fa-solid fa-plus"></i> Add Logo
                    </button>
                </div>

                <input type="hidden" name="client_logo_count" id="client-logo-count-input" value="${logoCount}">

                <div class="vcms-scroll-wrapper" id="client-logos-container" style="max-height: 55vh; overflow-y: auto; padding-right: 5px;">
                    ${listHTML || '<div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>No client logos yet. Click "Add Logo" to add one.</div>'}
                </div>

                <div style="margin-top: 20px; padding: 14px 18px; background: rgba(255, 152, 0, 0.1); border-left: 4px solid #ff9800; border-radius: 8px; font-size: 13px; color: #ffb74d;">
                    <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i> 
                    <strong>Tip:</strong> Use SVG or PNG with transparent background for best results. Recommended size: 150x80px.
                </div>
            `;
        },

        // Add new client logo
        addClientLogo: function () {
            const container = $("#client-logos-container");
            const currentCount = container.find(".vcms-client-logo-item").length;
            
            // Find max existing key and add 1 for new logo
            let maxKey = -1;
            container.find(".vcms-client-logo-item").each(function() {
                const key = parseInt($(this).data("logo-key"), 10);
                if (!isNaN(key) && key > maxKey) {
                    maxKey = key;
                }
            });
            const newIndex = maxKey + 1;
            
            const defaultLogo = "images/client-logo-1.svg";
            
            const newLogoHTML = `
                <div class="vcms-client-logo-item" data-logo-key="${newIndex}" style="background: #252525; border: 2px solid #ffcc01; padding: 15px; border-radius: 12px; margin-bottom: 12px; display: flex; align-items: center; gap: 15px; box-shadow: 0 0 15px rgba(255, 204, 1, 0.15);">
                    <div style="width: 80px; height: 50px; background: #161616; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #3a3a3a; flex-shrink: 0;">
                        <img id="client-logo-preview-${newIndex}" src="/${defaultLogo}" style="max-width: 70px; max-height: 40px; object-fit: contain;">
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-weight: 600; color: #ffcc01; font-size: 13px;"><i class="fa-solid fa-plus-circle" style="margin-right: 5px;"></i>New Logo</span>
                            <button type="button" class="btn btn-sm" onclick="VCMSAdmin.deleteClientLogo('${newIndex}'); return false;" style="background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; padding: 4px 10px; border-radius: 6px; font-size: 11px;">
                                <i class="fa-solid fa-trash" style="pointer-events: none;"></i>
                            </button>
                        </div>
                        <input type="file" class="vcms-input vcms-client-logo-file" name="client_logo_${newIndex}" accept="image/*" data-preview="client-logo-preview-${newIndex}" style="font-size: 12px; padding: 6px;">
                        <input type="hidden" name="client_logo_${newIndex}_new" value="1">
                        <input type="hidden" name="client_logo_${newIndex}_current" value="${defaultLogo}">
                    </div>
                </div>
            `;
            
            // Remove empty message if exists
            container.find('[style*="text-align: center"]').remove();
            
            container.append(newLogoHTML);
            
            // Update count
            const newCount = container.find(".vcms-client-logo-item").length;
            $("#client-logos-count").text(newCount);
            $("#client-logo-count-input").val(newCount);
            
            // Scroll to new item
            container.animate({ scrollTop: container[0].scrollHeight }, 300);
            
            this.showNotification("New logo added. Upload an image and save.", "success");
        },

        // Delete client logo
        deleteClientLogo: function (logoKey) {
            if (confirm("Are you sure you want to delete this client logo?")) {
                const $item = $(`.vcms-client-logo-item[data-logo-key="${logoKey}"]`);
                
                // Check if logo exists in database (has data-from-db="1")
                const existsInDb = $item.data("from-db") === 1 || $item.data("from-db") === "1";
                
                if (existsInDb) {
                    $(".vcms-edit-modal-body").append(
                        `<input type="hidden" name="delete_client_logo_${logoKey}" value="1">`
                    );
                }
                
                // Remove with animation
                $item.css({
                    "background": "#ffebee",
                    "border-color": "#f44336",
                    "opacity": "0.5"
                });
                
                setTimeout(() => {
                    $item.slideUp(300, function() {
                        $(this).remove();
                        const newCount = $("#client-logos-container .vcms-client-logo-item").length;
                        $("#client-logos-count").text(newCount);
                        $("#client-logo-count-input").val(newCount);
                    });
                }, 300);
                
                this.showNotification("Logo marked for deletion", "success");
            }
        },

        // =======================================
        // SAVE CHANGES
        // =======================================
        saveChanges: function () {
            const self = this;

            // Prevent double save - check immediately and set flag
            if (this.isSaving) {
                console.log("Save already in progress, ignoring...");
                return;
            }
            
            // Set saving flag IMMEDIATELY to prevent double clicks
            this.isSaving = true;
            
            // Disable save button immediately
            $(".btn-save-modal")
                .prop("disabled", true)
                .addClass("saving")
                .html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

            // Get section info
            const section = this.currentEditItem.closest(".vcms-editable-section");
            const sectionKey = section.data("section") || "unknown";

            // Get item info if editing specific item
            const itemId = this.currentEditItem.data("item-id") || "default";

            // Collect form data
            const formData = new FormData();
            formData.append("section_key", sectionKey);
            formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

            // Track files and content separately
            const contentData = [];
            let hasFiles = false;
            let fileCount = 0;

            // Collect all inputs
            $(".vcms-edit-modal-body input, .vcms-edit-modal-body textarea, .vcms-edit-modal-body select").each(function () {
                const name = $(this).attr("name");
                const value = $(this).val();

                // Skip if no name attribute
                if (!name) return;

                // Skip hidden inputs for current paths
                if (name === "current_image_path") return;

                // Check if it's a file input
                if ($(this).is('[type="file"]')) {
                    // Only process if file is selected
                    if (this.files && this.files.length > 0) {
                        const file = this.files[0];
                        
                        // Validate file type
                        if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                            self.showNotification("Invalid file type: " + file.name, "error");
                            return;
                        }
                        
                        // Validate file size (10MB max)
                        if (file.size > 10 * 1024 * 1024) {
                            self.showNotification("File too large: " + file.name + " (max 10MB)", "error");
                            return;
                        }

                        formData.append("files[" + name + "]", file);
                        hasFiles = true;
                        fileCount++;
                        
                        // Special handling for project image - save as media_src
                        if (name.match(/^project_(.+)_image$/)) {
                            const projectId = name.match(/^project_(.+)_image$/)[1];
                            contentData.push({
                                key: itemId + "_project_" + projectId + "_media_src",
                                value: "pending_upload", // Will be updated with file path on server
                                type: file.type.startsWith('image/') ? "image" : "video",
                            });
                        } else if (name.match(/^service_(.+)_image_file$/)) {
                            // Special handling for service background image file upload
                            const serviceId = name.match(/^service_(.+)_image_file$/)[1];
                            contentData.push({
                                key: itemId + "_service_" + serviceId + "_image",
                                value: "pending_upload", // Will be updated with file path on server
                                type: "image",
                            });
                        } else if (name.match(/^tool_(.+)_icon_file$/)) {
                            // Special handling for tool icon file upload
                            const toolId = name.match(/^tool_(.+)_icon_file$/)[1];
                            contentData.push({
                                key: itemId + "_tool_" + toolId + "_icon",
                                value: "pending_upload",
                                type: "image",
                            });
                        } else if (name.match(/^client_logo_(.+)$/)) {
                            // Special handling for client logo file upload
                            const logoKey = name.match(/^client_logo_(.+)$/)[1];
                            contentData.push({
                                key: itemId + "_client_logo_" + logoKey,
                                value: "pending_upload", // Will be updated with file path on server
                                type: "image",
                            });
                        } else if (name === "footer_logo") {
                            // Special handling for footer logo file upload
                            contentData.push({
                                key: "footer_footer_logo_file",
                                value: "pending_upload",
                                type: "image",
                            });
                            console.log("Footer logo file added");
                        } else {
                            contentData.push({
                                key: itemId + "_" + name,
                                value: "pending_upload", // Will be updated with file path on server
                                type: file.type.startsWith('image/') ? "image" : "video",
                            });
                        }
                        
                        console.log("File added:", name, file.name, file.size);
                    }
                } else if ($(this).is('[type="hidden"]')) {
                    // Handle hidden inputs (like delete markers and reset markers)
                    if (name.startsWith("delete_image_")) {
                        contentData.push({
                            key: itemId + "_" + name,
                            value: value,
                            type: "delete",
                        });
                    } else if (name.startsWith("delete_service_")) {
                        // Handle service deletion
                        const serviceId = name.replace("delete_service_", "");
                        contentData.push({
                            key: itemId + "_delete_service_" + serviceId,
                            value: serviceId,
                            type: "delete_service",
                        });
                        console.log("Delete service:", serviceId);
                    } else if (name.startsWith("delete_member_")) {
                        // Handle team member deletion
                        const memberIndex = name.replace("delete_member_", "");
                        contentData.push({
                            key: itemId + "_delete_member_" + memberIndex,
                            value: memberIndex,
                            type: "delete_member",
                        });
                        console.log("Delete team member:", memberIndex);
                    } else if (name.startsWith("delete_client_logo_")) {
                        // Handle client logo deletion
                        const logoIndex = name.replace("delete_client_logo_", "");
                        contentData.push({
                            key: itemId + "_delete_client_logo_" + logoIndex,
                            value: logoIndex,
                            type: "delete_client_logo",
                        });
                        console.log("Delete client logo:", logoIndex);
                    } else if (name.startsWith("delete_list_item_")) {
                        // Handle about list item deletion
                        const itemIndex = name.replace("delete_list_item_", "");
                        contentData.push({
                            key: itemId + "_delete_list_item_" + itemIndex,
                            value: itemIndex,
                            type: "delete_list_item",
                        });
                        console.log("Delete list item:", itemIndex);
                    } else if (name.startsWith("delete_vm_list_")) {
                        // Handle vision/mission list item deletion
                        const parts = name.replace("delete_vm_list_", "").split("_");
                        const vmType = parts[0]; // vision or mission
                        const itemIndex = parts[1];
                        contentData.push({
                            key: itemId + "_delete_vm_list_" + vmType + "_" + itemIndex,
                            value: vmType + "_" + itemIndex,
                            type: "delete_vm_list",
                        });
                        console.log("Delete VM list item:", vmType, itemIndex);
                    } else if (name.startsWith("delete_plan_")) {
                        // Handle pricing plan feature deletion
                        // Format: delete_plan_basic_feature_1 -> planId=basic, featureIndex=1
                        const parts = name.replace("delete_plan_", "").split("_feature_");
                        const planId = parts[0];
                        const featureIndex = parts[1];
                        contentData.push({
                            key: itemId + "_delete_plan_" + planId + "_feature_" + featureIndex,
                            value: planId + "_" + featureIndex,
                            type: "delete_pricing_feature",
                        });
                        console.log("Delete pricing feature:", planId, featureIndex);
                    } else if (name.startsWith("reset_")) {
                        // Handle reset to default - send with type "reset" so server knows to delete old media
                        const fieldName = name.replace("reset_", "");
                        contentData.push({
                            key: itemId + "_" + fieldName,
                            value: value, // This is the default image path
                            type: "reset", // Special type to trigger media deletion on server
                        });
                        console.log("Reset to default (will delete old media):", fieldName, "->", value);
                    } else if (name.endsWith("_new") && value === "1") {
                        // Mark new service or project
                        if (name.startsWith("service_")) {
                            const serviceId = name.replace("_new", "").replace("service_", "");
                            contentData.push({
                                key: itemId + "_new_service_" + serviceId,
                                value: "1",
                                type: "new_service",
                            });
                            console.log("New service:", serviceId);
                        } else if (name.startsWith("project_")) {
                            const projectId = name.replace("_new", "").replace("project_", "");
                            contentData.push({
                                key: itemId + "_new_project_" + projectId,
                                value: "1",
                                type: "new_project",
                            });
                            console.log("New project:", projectId);
                        } else if (name.startsWith("client_logo_")) {
                            // Mark new client logo (for reference)
                            console.log("New client logo:", name);
                        }
                    } else if (name.match(/^client_logo_(.+)_current$/)) {
                        // Handle client logo current value (save if no file uploaded)
                        const logoKey = name.match(/^client_logo_(.+)_current$/)[1];
                        
                        // Check if a file was uploaded for this logo
                        const fileInput = $(`input[name="client_logo_${logoKey}"]`);
                        const hasNewFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
                        
                        // Check if logo is from database (should be saved)
                        const $logoItem = $(`.vcms-client-logo-item[data-logo-key="${logoKey}"]`);
                        const fromDb = $logoItem.data("from-db") === 1 || $logoItem.data("from-db") === "1";
                        
                        // Check if this is a newly added logo
                        const isNewLogo = $(`input[name="client_logo_${logoKey}_new"]`).length > 0;
                        
                        // Only save current value if logo exists in DB (preserve existing)
                        // New logos and default logos should only be saved when file is uploaded
                        if (!hasNewFile && value && fromDb && !isNewLogo) {
                            // No new file uploaded, but logo exists in DB - save current value
                            contentData.push({
                                key: itemId + "_client_logo_" + logoKey,
                                value: value,
                                type: "image",
                            });
                            console.log("Saving current client logo:", logoKey, "->", value);
                        }
                    } else if (name.startsWith("delete_project_")) {
                        // Handle project deletion
                        const projectId = name.replace("delete_project_", "");
                        contentData.push({
                            key: itemId + "_delete_project_" + projectId,
                            value: projectId,
                            type: "delete_project",
                        });
                        console.log("Delete project:", projectId);
                    } else if (name.startsWith("delete_tool_")) {
                        // Handle tool deletion
                        const toolId = name.replace("delete_tool_", "");
                        contentData.push({
                            key: itemId + "_delete_tool_" + toolId,
                            value: toolId,
                            type: "delete_tool",
                        });
                        console.log("Delete tool:", toolId);
                    } else if (name.startsWith("delete_process_")) {
                        // Handle process step deletion
                        const processId = name.replace("delete_process_", "");
                        contentData.push({
                            key: itemId + "_delete_process_" + processId,
                            value: processId,
                            type: "delete_process",
                        });
                        console.log("Delete process:", processId);
                    } else if (name.startsWith("delete_achievement_")) {
                        // Handle achievement deletion
                        const achievementId = name.replace("delete_achievement_", "");
                        contentData.push({
                            key: itemId + "_delete_achievement_" + achievementId,
                            value: achievementId,
                            type: "delete_achievement",
                        });
                        console.log("Delete achievement:", achievementId);
                    } else if (name.match(/^tool_(.+)_icon_current$/)) {
                        // Handle tool icon current value
                        const toolId = name.match(/^tool_(.+)_icon_current$/)[1];
                        const fileInput = $(`input[name="tool_${toolId}_icon_file"]`);
                        const hasNewFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
                        
                        if (!hasNewFile && value) {
                            // No new file uploaded, save current value
                            contentData.push({
                                key: itemId + "_tool_" + toolId + "_icon",
                                value: value,
                                type: "image",
                            });
                        }
                    } else if (name.endsWith("_exists") && value === "1") {
                        // Skip exists markers, just for tracking
                    } else if (name === "image_current") {
                        // Handle about section image current value
                        const fileInput = $(`input[name="image"]`);
                        const hasNewFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
                        
                        if (!hasNewFile && value) {
                            // No new file uploaded, save current value
                            contentData.push({
                                key: itemId + "_image",
                                value: value,
                                type: "image",
                            });
                        }
                    } else if (name === "vision_image_current" || name === "mission_image_current") {
                        // Handle vision/mission image current value
                        const imageType = name.replace("_image_current", "");
                        const fileInput = $(`input[name="${imageType}_image"]`);
                        const hasNewFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
                        
                        if (!hasNewFile && value) {
                            // No new file uploaded, save current value
                            contentData.push({
                                key: itemId + "_" + imageType + "_image",
                                value: value,
                                type: "image",
                            });
                        }
                    } else if (name.match(/^service_(.+)_image$/)) {
                        // Handle service image current value - skip if file is being uploaded
                        const serviceId = name.match(/^service_(.+)_image$/)[1];
                        const fileInputName = `service_${serviceId}_image_file`;
                        const fileInput = $(`input[name="${fileInputName}"]`);
                        const hasNewFile = fileInput.length > 0 && fileInput[0].files && fileInput[0].files.length > 0;
                        
                        if (!hasNewFile && value) {
                            // No new file uploaded, save current value
                            contentData.push({
                                key: itemId + "_service_" + serviceId + "_image",
                                value: value,
                                type: "image",
                            });
                            console.log("Saving current service image:", serviceId, "->", value);
                        }
                    } else {
                        // Save other hidden inputs (like skills_count, expertise_count, etc.)
                        contentData.push({
                            key: itemId + "_" + name,
                            value: value || "",
                            type: "text",
                        });
                    }
                } else {
                    // Save all other inputs
                    const inputType = $(this).is("textarea") ? "textarea" : 
                                      $(this).is("select") ? "select" : "text";
                    
                    // Keep content type as text for most fields
                    let contentType = inputType;
                    
                    // Special handling for project fields
                    if (name.match(/^project_(.+)_video_url$/)) {
                        // This is a video URL, save as media_src
                        const projectId = name.match(/^project_(.+)_video_url$/)[1];
                        const mediaType = $(`select[name="project_${projectId}_media_type"]`).val();
                        
                        if (mediaType === "video" && value) {
                            // Convert YouTube URL to embed format with nocookie
                            const embedUrl = self.convertYouTubeUrl(value);
                            contentData.push({
                                key: itemId + "_project_" + projectId + "_media_src",
                                value: embedUrl,
                                type: "url",
                            });
                        }
                    } else if (name.match(/^project_(.+)_media_type$/)) {
                        // Save media type - get value directly from select element
                        const selectValue = $(this).val();
                        contentData.push({
                            key: itemId + "_" + name,
                            value: selectValue || value || "video",
                            type: "text",
                        });
                    } else {
                        // Skip video_url field if already handled above, and skip _image fields (file inputs)
                        if (name.match(/^project_(.+)_video_url$/) || name.match(/^project_(.+)_image$/)) {
                            // Already handled or will be handled by file upload
                            return;
                        }
                        
                        // Skip current_image hidden fields
                        if (name.match(/^project_(.+)_current_image$/)) {
                            return;
                        }

                        // Special handling for CTA section
                        let finalKey;
                        if (sectionKey === "cta") {
                            // For CTA section, use special key format
                            if (name === "text_content") {
                                // text_content -> cta_text_content
                                finalKey = "cta_text_content";
                            } else if (name.match(/^cnt_(\d+)_(num|sym|label)$/)) {
                                // cnt_0_num -> cta-counters_cnt_0_num
                                finalKey = "cta-counters_" + name;
                            } else {
                                // fallback to normal behavior
                                finalKey = itemId + "_" + name;
                            }
                        } else {
                            finalKey = itemId + "_" + name;
                        }

                        contentData.push({
                            key: finalKey,
                            value: value || "",
                            type: contentType,
                        });
                    }
                }
            });

            // Deduplicate contentData - keep last occurrence of each key (prefer pending_upload over existing value)
            const uniqueContentData = [];
            const seenKeys = new Map();
            
            // Process in reverse to keep the last (most recent) entry for each key
            for (let i = contentData.length - 1; i >= 0; i--) {
                const item = contentData[i];
                const trackKey = item.key + "_" + item.type;
                if (!seenKeys.has(trackKey)) {
                    seenKeys.set(trackKey, true);
                    uniqueContentData.unshift(item);
                } else {
                    console.log("Duplicate removed:", item.key, item.type);
                }
            }
            
            formData.append("contents", JSON.stringify(uniqueContentData));

            // Get CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr("content");

            // Show loading state
            const loadingText = hasFiles ? 
                '<i class="fa-solid fa-spinner fa-spin"></i> Uploading...' : 
                '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
            
            $(".btn-save-modal").html(loadingText).prop("disabled", true);

            // Send to API
            $.ajax({
                url: "/admin/api/content/batch-update",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    // Upload progress
                    if (hasFiles) {
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $(".btn-save-modal").html(
                                    '<i class="fa-solid fa-spinner fa-spin"></i> Uploading ' + percentComplete + '%'
                                );
                            }
                        }, false);
                    }
                    return xhr;
                },
                success: function (response) {
                    if (response.success) {
                        // Block entire page to prevent any further interaction
                        $("body").append('<div id="vcms-reload-overlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:99999;display:flex;align-items:center;justify-content:center;"><div style="color:#fff;text-align:center;"><i class="fa-solid fa-spinner fa-spin" style="font-size:48px;margin-bottom:20px;display:block;"></i>Saved! Reloading...</div></div>');
                        
                        // Reload page immediately
                        window.location.reload();
                    } else {
                        self.isSaving = false;
                        self.showNotification(response.message || "Save failed!", "error");
                        $(".btn-save-modal").html("Save").prop("disabled", false);
                    }
                },
                error: function (xhr) {
                    console.error("Save error:", xhr);
                    self.isSaving = false;
                    const message = xhr.responseJSON?.message || "Error saving changes!";
                    self.showNotification(message, "error");
                    $(".btn-save-modal").html("Save").prop("disabled", false);
                },
            });
        },

        // =======================================
        // SAVE ALL CHANGES
        // =======================================
        saveAllChanges: function () {
            const self = this;

            // Show loading state
            $(".btn-save-changes")
                .html(
                    '<i class="fa-solid fa-spinner fa-spin"></i> Publishing...'
                )
                .prop("disabled", true);

            // TODO: Replace with actual AJAX call to publish all changes

            // Simulate AJAX save all (remove this in production)
            setTimeout(function () {
                self.showNotification(
                    "All changes published successfully!",
                    "success"
                );
                $(".btn-save-changes")
                    .html('<i class="fa-solid fa-save"></i> Save Changes')
                    .prop("disabled", false);
            }, 1500);
        },

        // =======================================
        // SHOW NOTIFICATION
        // =======================================
        showNotification: function (message, type) {
            type = type || "success";
            const iconClass =
                type === "success"
                    ? "fa-check-circle"
                    : "fa-exclamation-circle";

            const notification = $(`
                <div class="vcms-notification ${type}">
                    <i class="fa-solid ${iconClass}"></i>
                    <span>${message}</span>
                </div>
            `);

            $("body").append(notification);

            setTimeout(function () {
                notification.addClass("show");
            }, 100);

            setTimeout(function () {
                notification.removeClass("show");
                setTimeout(function () {
                    notification.remove();
                }, 300);
            }, 3000);
        },

        // =======================================
        // CTA SECTION FORM GENERATOR
        // =======================================
        generateCtaSectionForm: function (element) {
            const self = this;

            // Get CTA text content
            const ctaText = element.find(".cta-box-content p").text().trim() || "";

            // Get counters data
            let countersHTML = "";
            element.find(".cta-counter-item").each(function (index) {
                const item = $(this);
                const number = item.find(".counter").text().trim();
                const fullH3 = item.find("h3").text().trim();
                const symbol = fullH3.replace(number, "").trim();
                const label = item.find("p").text().trim();

                countersHTML += `
                    <div class="vcms-list-item-box" style="background: #252525; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 15px;">
                        <div style="display: grid; grid-template-columns: 2fr 1fr 3fr; gap: 15px; align-items: end;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; color: #e0e0e0; font-size: 13px; font-weight: 600;">Number (Animated)</label>
                                <input type="number" class="vcms-input" name="cnt_${index}_num" value="${self.escapeHTML(number)}" placeholder="e.g. 45">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; color: #e0e0e0; font-size: 13px; font-weight: 600;">Symbol</label>
                                <input type="text" class="vcms-input" name="cnt_${index}_sym" value="${self.escapeHTML(symbol)}" placeholder="+ or %">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; color: #e0e0e0; font-size: 13px; font-weight: 600;">Label</label>
                                <input type="text" class="vcms-input" name="cnt_${index}_label" value="${self.escapeHTML(label)}" placeholder="e.g. project completed">
                            </div>
                        </div>
                    </div>
                `;
            });

            return `
                <div style="background: #1e1e1e; border: 1px solid #3a3a3a; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                    <h4 style="color: #ffcc01; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-bullhorn"></i> CTA Section Settings
                    </h4>
                    <p style="color: #b0b0b0; font-size: 13px; margin: 0;">
                        Edit the Call-to-Action description text and statistics counters.
                    </p>
                </div>

                <div class="vcms-form-group">
                    <label>CTA Description Text</label>
                    <textarea class="vcms-textarea" name="text_content" rows="5" placeholder="Enter CTA description...">${self.escapeHTML(ctaText)}</textarea>
                    <small style="color: #888; font-size: 12px; margin-top: 5px; display: block;">
                        This text appears at the top of the CTA section.
                    </small>
                </div>

                <div style="background: #1e1e1e; border: 1px solid #3a3a3a; padding: 16px; border-radius: 12px; margin: 25px 0 15px;">
                    <i class="fa-solid fa-calculator" style="color: #ffcc01; margin-right: 8px;"></i>
                    <strong style="color: #ffcc01;">Statistics Counters</strong>
                    <span style="color: #888; margin-left: 10px;">(${element.find(".cta-counter-item").length} items)</span>
                </div>
                <div class="vcms-scroll-wrapper">
                    ${countersHTML}
                </div>

                <div class="alert alert-info" style="margin-top: 20px;">
                    <i class="fa-solid fa-info-circle"></i>
                    <strong>Note:</strong> Changes will be applied to the CTA Section immediately after saving.
                </div>
            `;
        },

        // Generate Footer Form
        generateFooterForm: function(element) {
            const self = this;
            const $item = $(element).find('.vcms-editable-item[data-edit-type="footer-content"]');
            if (!$item.length) {
                // Fallback if called from section level
                return this.generateFooterFormFromSection(element);
            }
            
            // Get data from attributes using attr() to avoid jQuery camelCase conversion
            const title = $item.attr("data-title") || "Let's Connect";
            const titleLink = $item.attr("data-title-link") || "";
            const logo = $item.attr("data-logo") || "images/logo-dikelolain-full-hitam.png";
            const description = $item.attr("data-description") || "";
            const socialFb = $item.attr("data-social-fb") || "#";
            const socialX = $item.attr("data-social-x") || "#";
            const socialLi = $item.attr("data-social-li") || "#";
            const socialIg = $item.attr("data-social-ig") || "#";
            const quick1Text = $item.attr("data-quick-1-text") || "home";
            const quick1Url = $item.attr("data-quick-1-url") || "";
            const quick2Text = $item.attr("data-quick-2-text") || "about us";
            const quick2Url = $item.attr("data-quick-2-url") || "";
            const quick3Text = $item.attr("data-quick-3-text") || "team";
            const quick3Url = $item.attr("data-quick-3-url") || "";
            const quick4Text = $item.attr("data-quick-4-text") || "our portfolio";
            const quick4Url = $item.attr("data-quick-4-url") || "";
            const service1Text = $item.attr("data-service-1-text") || "UI/UX Design";
            const service1Url = $item.attr("data-service-1-url") || "";
            const service2Text = $item.attr("data-service-2-text") || "3D Designs";
            const service2Url = $item.attr("data-service-2-url") || "";
            const service3Text = $item.attr("data-service-3-text") || "Web Development";
            const service3Url = $item.attr("data-service-3-url") || "";
            const service4Text = $item.attr("data-service-4-text") || "Motion Graphics";
            const service4Url = $item.attr("data-service-4-url") || "";
            const contactPhone = $item.attr("data-contact-phone") || "";
            const contactEmail = $item.attr("data-contact-email") || "";
            const contactAddress = $item.attr("data-contact-address") || "";
            const copyright = $item.attr("data-copyright") || "";

            return `
                <div style="max-height: 75vh; overflow-y: auto; padding-right: 10px;">
                    <!-- Footer Title Section -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-heading"></i> Footer Title
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label>Title Text</label>
                                <input type="text" class="vcms-input" name="footer_title" value="${self.escapeHTML(title)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label>Title Link URL</label>
                                <input type="text" class="vcms-input" name="footer_title_link" value="${self.escapeHTML(titleLink)}">
                            </div>
                        </div>
                    </div>

                    <!-- About Footer Section -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-building"></i> About Footer
                        </h4>
                        <div class="vcms-form-group">
                            <label><i class="fa-solid fa-image"></i> Footer Logo</label>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="width: 150px; height: 60px; background: #1a1a1a; border-radius: 8px; overflow: hidden; border: 2px solid #3a3a3a; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <img id="footer-logo-preview" src="/${logo}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Footer Logo">
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" class="vcms-input vcms-file-input" name="footer_logo" accept="image/*" data-preview="footer-logo-preview" style="font-size: 12px;">
                                    <input type="hidden" name="current_footer_logo" value="${self.escapeHTML(logo)}">
                                    <p style="margin: 8px 0 0 0; font-size: 11px; color: #888;">Recommended: PNG with transparent background</p>
                                </div>
                            </div>
                        </div>
                        <div class="vcms-form-group" style="margin-bottom: 0;">
                            <label>Footer Description</label>
                            <textarea class="vcms-textarea" name="footer_description" rows="3">${self.escapeHTML(description)}</textarea>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-share-nodes"></i> Social Media Links
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-brands fa-facebook-f" style="color: #4267B2;"></i> Facebook</label>
                                <input type="text" class="vcms-input" name="footer_social_fb" value="${self.escapeHTML(socialFb)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-brands fa-x-twitter"></i> X / Twitter</label>
                                <input type="text" class="vcms-input" name="footer_social_x" value="${self.escapeHTML(socialX)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-brands fa-linkedin-in" style="color: #0077B5;"></i> LinkedIn</label>
                                <input type="text" class="vcms-input" name="footer_social_li" value="${self.escapeHTML(socialLi)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-brands fa-instagram" style="color: #E4405F;"></i> Instagram</label>
                                <input type="text" class="vcms-input" name="footer_social_ig" value="${self.escapeHTML(socialIg)}">
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-link"></i> Quick Links
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <input type="text" class="vcms-input" name="footer_quick_1_text" value="${self.escapeHTML(quick1Text)}" placeholder="Link 1 Text">
                            <input type="text" class="vcms-input" name="footer_quick_1_url" value="${self.escapeHTML(quick1Url)}" placeholder="Link 1 URL">
                            <input type="text" class="vcms-input" name="footer_quick_2_text" value="${self.escapeHTML(quick2Text)}" placeholder="Link 2 Text">
                            <input type="text" class="vcms-input" name="footer_quick_2_url" value="${self.escapeHTML(quick2Url)}" placeholder="Link 2 URL">
                            <input type="text" class="vcms-input" name="footer_quick_3_text" value="${self.escapeHTML(quick3Text)}" placeholder="Link 3 Text">
                            <input type="text" class="vcms-input" name="footer_quick_3_url" value="${self.escapeHTML(quick3Url)}" placeholder="Link 3 URL">
                            <input type="text" class="vcms-input" name="footer_quick_4_text" value="${self.escapeHTML(quick4Text)}" placeholder="Link 4 Text">
                            <input type="text" class="vcms-input" name="footer_quick_4_url" value="${self.escapeHTML(quick4Url)}" placeholder="Link 4 URL">
                        </div>
                    </div>

                    <!-- Services Links -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-concierge-bell"></i> Services Links
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <input type="text" class="vcms-input" name="footer_service_1_text" value="${self.escapeHTML(service1Text)}" placeholder="Service 1 Text">
                            <input type="text" class="vcms-input" name="footer_service_1_url" value="${self.escapeHTML(service1Url)}" placeholder="Service 1 URL">
                            <input type="text" class="vcms-input" name="footer_service_2_text" value="${self.escapeHTML(service2Text)}" placeholder="Service 2 Text">
                            <input type="text" class="vcms-input" name="footer_service_2_url" value="${self.escapeHTML(service2Url)}" placeholder="Service 2 URL">
                            <input type="text" class="vcms-input" name="footer_service_3_text" value="${self.escapeHTML(service3Text)}" placeholder="Service 3 Text">
                            <input type="text" class="vcms-input" name="footer_service_3_url" value="${self.escapeHTML(service3Url)}" placeholder="Service 3 URL">
                            <input type="text" class="vcms-input" name="footer_service_4_text" value="${self.escapeHTML(service4Text)}" placeholder="Service 4 Text">
                            <input type="text" class="vcms-input" name="footer_service_4_url" value="${self.escapeHTML(service4Url)}" placeholder="Service 4 URL">
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-address-card"></i> Contact Info
                        </h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-solid fa-phone"></i> Phone</label>
                                <input type="text" class="vcms-input" name="footer_contact_phone" value="${self.escapeHTML(contactPhone)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-solid fa-envelope"></i> Email</label>
                                <input type="email" class="vcms-input" name="footer_contact_email" value="${self.escapeHTML(contactEmail)}">
                            </div>
                            <div class="vcms-form-group" style="margin-bottom: 0;">
                                <label><i class="fa-solid fa-location-dot"></i> Address</label>
                                <input type="text" class="vcms-input" name="footer_contact_address" value="${self.escapeHTML(contactAddress)}">
                            </div>
                        </div>
                    </div>

                    <!-- Copyright -->
                    <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                        <h4 style="color: #ffcc01; margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #ffcc01;">
                            <i class="fa-solid fa-copyright"></i> Copyright
                        </h4>
                        <div class="vcms-form-group" style="margin-bottom: 0;">
                            <label>Copyright Text</label>
                            <input type="text" class="vcms-input" name="footer_copyright" value="${self.escapeHTML(copyright)}">
                        </div>
                    </div>
                </div>
            `;
        },

        generateFooterFormFromSection: function(element) {
            // Fallback when called from section level - find the footer-content item
            const $item = $(element).find('[data-edit-type="footer-content"]');
            if ($item.length) {
                return this.generateFooterForm($item);
            }
            return '<p style="color: #888; text-align: center;">Footer content not found.</p>';
        },

        // =======================================
        // HELPER FUNCTIONS
        // =======================================
        stripHTML: function (html) {
            return $("<div>").html(html).text();
        },

        escapeHTML: function (text) {
            // Convert to string first to handle numbers and other types
            if (text === null || text === undefined) {
                return '';
            }
            const str = String(text);
            const map = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            };
            return str.replace(/[&<>"']/g, function (m) {
                return map[m];
            });
        },
    };

    // =======================================
    // EXPOSE TO GLOBAL SCOPE
    // =======================================
    window.VCMSAdmin = VCMS;

    // =======================================
    // INITIALIZE ON DOCUMENT READY
    // =======================================
    $(document).ready(function () {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        VCMS.init();

    });
})(jQuery);
