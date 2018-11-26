import { Component, Mixin } from 'src/core/shopware';
import template from './sw-media-modal-delete.html.twig';
import './sw-media-modal-delete.less';

/**
 * @private
 */
Component.register('sw-media-modal-delete', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    props: {
        itemsToDelete: {
            required: true,
            type: Array,
            validator(value) {
                return (value.length !== 0);
            }
        }
    },

    methods: {
        closeDeleteModal(originalDomEvent) {
            this.$emit('sw-media-modal-delete-close', { originalDomEvent });
        },

        deleteSelection() {
            const deletePromises = [];
            const NotificationMessageSuccess = this.$tc('global.sw-media-modal-delete.notificationSuccessOverall');
            const NotificationMessageError = this.$tc('global.sw-media-modal-delete.notificationErrorOverall');

            this.itemsToDelete.forEach((item) => {
                const messages = this._getNotificationMessages(item);
                item.isLoading = true;

                deletePromises.push(
                    item.delete(true).then(() => {
                        item.isLoading = false;
                        this.createNotificationSuccess({
                            message: messages.successMessage
                        });
                    }).catch(() => {
                        item.isLoading = false;
                        this.createNotificationError({
                            message: messages.errorMessage
                        });
                    })
                );
            });

            this.$emit(
                'sw-media-modal-delete-items-deleted',
                Promise.all(deletePromises).then(() => {
                    this.createNotificationSuccess({
                        message: NotificationMessageSuccess
                    });
                }).catch(() => {
                    this.createNotificationError({
                        message: NotificationMessageError
                    });
                })
            );
        },

        _getNotificationMessages(item) {
            const successMessage = this.$tc(
                'global.sw-media-modal-delete.notificationSuccessSingle',
                1,
                { mediaName: item.name }
            );
            const errorMessage = this.$tc(
                'global.sw-media-modal-delete.notificationErrorSingle',
                1,
                { mediaName: item.name }
            );

            return {
                successMessage,
                errorMessage
            };
        }
    }
});