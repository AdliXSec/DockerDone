from flask import Blueprint, jsonify, request
from app.controllers.user_controller import UserController
from flask_jwt_extended import jwt_required, get_jwt

user_bp = Blueprint('user_bp', __name__)

def response(status, messages, data):
    return {
        "status": status, 
        "message": messages, 
        "data": data
    }


@user_bp.route('/users', methods=['GET'])
def get_users():
    users = UserController.get_all_users()
    return jsonify(response("berhasil", "Data berhasil diambil", users)), 200

@user_bp.route('/users/<int:user_id>', methods=['GET'])
def get_user(user_id):
    
    user = UserController.get_user_by_id(user_id)
    if user:
        return jsonify(response("berhasil", "Data berhasil diambil", user)), 200
    return jsonify(response("gagal", "Data tidak ditemukan", None)), 404

@user_bp.route('/users/<int:user_id>', methods=['PUT'])
@jwt_required()
def update_user(user_id):
    data = request.get_json()
    if not data or 'name' not in data or 'email' not in data:
        return jsonify(response("gagal", "Missing name, email, or password", None)), 400
    
    password = None
    if 'password' in data:
        password = data['password']
    else:
        password = None
        
    role = data.get('role', 'user')
    user = UserController.update_user(user_id, data['name'], data['email'], password, role)
    if user:
        return jsonify(response("berhasil", "Data berhasil diupdate", user)), 200
    return jsonify(response("gagal", "Data tidak ditemukan", None)), 404

@user_bp.route('/users/<int:user_id>', methods=['DELETE'])
@jwt_required()
def delete_user(user_id):
    user = UserController.delete_user(user_id)
    if user:
        return jsonify(response("berhasil", "Data berhasil dihapus", None)), 200 
    return jsonify(response("gagal", "Data tidak ditemukan", None)), 404

@user_bp.route('/is_login', methods=['GET'])
@jwt_required()
def is_login():
    user_id = get_jwt().get("sub")
    user = UserController.get_user_by_id(user_id)
    if user:
        return jsonify(response("berhasil", "Data berhasil diambil", user)), 200
    return jsonify(response("gagal", "Data tidak ditemukan", None)), 404
    
# @user_bp.route('/users', methods=['POST'])
# def create_user():
#     data = request.get_json()
#     if not data or 'name' not in data or 'email' not in data or 'password' not in data:
#         return jsonify({"error": "Missing name, email, or password"}), 400
    
#     role = data.get('role', 'user')
#     user = UserController.create_user(data['name'], data['email'], data['password'], role)
#     return jsonify(user), 201
