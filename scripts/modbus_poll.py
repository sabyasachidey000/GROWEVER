from pymodbus.client import ModbusTcpClient

client = ModbusTcpClient(
    host="192.168.1.114",
    port=502
)

client.connect()

result = client.write_coil(
    address=0,
    value=True,
    device_id=1
)

print(result)

client.close()