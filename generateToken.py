import jwt
import datetime

SECRET_KEY = "clave_secreta_super_segura"

def generar_token():
    payload = {
        "email": "usuario@example.com",
        "iat": datetime.datetime.now(datetime.timezone.utc),
        "exp": datetime.datetime.now(datetime.timezone.utc) + datetime.timedelta(seconds=120)  # 120 segundos
    }
    token = jwt.encode(payload, SECRET_KEY, algorithm="HS256")

    with open("token.txt", "w") as file:
        file.write(token)

    print(f"Token generado: {token}")

if __name__ == "__main__":
    generar_token()
