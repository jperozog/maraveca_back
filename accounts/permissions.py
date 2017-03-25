from rest_framework import permissions


class IsSelf(permissions.BasePermission):
    def has_object_permission(self, request, view, obj):
        return request.user == obj or request.user.is_staff

    def has_permission(self, request, view):
        return request.user.is_staff