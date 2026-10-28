# Energy Meter Poller

Install dependencies:

```powershell
python -m pip install -r scripts\requirements.txt
```

Run one test reading:

```powershell
python scripts\modbus_poll.py --host 192.168.1.149 --machine-id 1 --once
```

Run continuously, inserting a row every 10 seconds:

```powershell
python scripts\modbus_poll.py --host 192.168.1.149 --machine-id 1 --interval 10
```

You can also map by machine code:

```powershell
python scripts\modbus_poll.py --host 192.168.1.149 --machine-code MCH-001
```

The script reads Laravel database values from `.env` by default:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

Optional energy meter overrides:

- `ENERGY_MODBUS_HOST`
- `ENERGY_MODBUS_PORT`
- `ENERGY_MODBUS_UNIT_ID`
- `ENERGY_MODBUS_REGISTER_TYPE`
- `ENERGY_MODBUS_BYTE_ORDER`
- `ENERGY_MODBUS_WORD_ORDER`
- `ENERGY_POLL_INTERVAL`
- `ENERGY_MACHINE_ID`
- `ENERGY_MACHINE_CODE`

For the `/machines` energy modal, each reading must have a matching `machine_id`.
Pass `--machine-id`, pass `--machine-code`, or set the machine `plc_ip_address`
to the energy meter IP so the script can auto-map it.
