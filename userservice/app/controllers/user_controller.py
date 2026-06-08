from app.models.user_model import User
from app import db, bcrypt

class UserController:
    @staticmethod
    def get_all_users():
        return [user.to_dict() for user in User.query.all()]

    @staticmethod
    def get_user_by_id(user_id):
        user = User.query.get(user_id)
        return user.to_dict() if user else None

    @staticmethod
    def create_user(name, email, password, role='user'):
        hashed_password = bcrypt.generate_password_hash(password).decode('utf-8')
        new_user = User(name=name, email=email, password=hashed_password, role=role)
        db.session.add(new_user)
        db.session.commit()
        return new_user.to_dict()
    
    @staticmethod
    def update_user(user_id, name, email, password, role='user'):
        user = User.query.get(user_id)
        if user:
            user.name = name
            user.email = email
            if password and password.strip() != "":
                user.password = bcrypt.generate_password_hash(password).decode('utf-8')
            db.session.commit()
            return user.to_dict()
        return None

    @staticmethod
    def delete_user(user_id):
        user = User.query.get(user_id)
        if user:
            db.session.delete(user)
            db.session.commit()
            return True
        return False