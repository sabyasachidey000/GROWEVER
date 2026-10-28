# Integration Architecture

## RFID Integration Architecture

```
┌─────────────┐     TCP/IP      ┌──────────────┐     HTTP API     ┌─────────────────┐
│  RFID       │ ──────────────► │  RFID        │ ───────────────► │  MES Backend    │
│  Reader     │                 │  Gateway     │                  │  (Laravel)      │
└─────────────┘                 └──────────────┘                  └────────┬────────┘
                                                                          │
                                                            ┌─────────────▼─────────┐
                                                            │  RfidService          │
                                                            │  - Validate RFID card │
                                                            │  - Create session     │
                                                            │  - Update machine     │
                                                            └─────────────┬─────────┘
                                                                          │
                                                            ┌─────────────▼─────────┐
                                                            │  OperatorSession      │
                                                            │  - Login/logout time  │
                                                            │  - Shift assignment   │
                                                            │  - Machine mapping    │
                                                            └───────────────────────┘
```

### Flow
1. RFID reader detects card swipe
2. Reader sends card number + machine ID to gateway
3. Gateway calls `POST /api/rfid/swipe`
4. MES validates employee, creates session, updates machine status
5. Response includes operator details and session info

### MQTT Integration Architecture

```
┌─────────────┐     MQTT        ┌──────────────┐     Queue Job     ┌─────────────────┐
│  PLC/Sensor │ ──────────────► │  MQTT Broker │ ────────────────► │  MES Subscriber │
│  (Machine)  │     pub/sub     │  (Mosquitto) │                   │  (Laravel)      │
└─────────────┘                 └──────────────┘                   └────────┬────────┘
                                                                           │
                                                             ┌─────────────▼──────────┐
                                                             │  MachineDataProcessor  │
                                                             │  - Parse PLC payload   │
                                                             │  - Update production   │
                                                             │  - Detect breakdowns   │
                                                             │  - Calculate OEE       │
                                                             └────────────────────────┘
```

### MQTT Topics

| Topic | Direction | Data |
|-------|-----------|------|
| `mes/machines/{id}/status` | Publish | `{"status": "running", "cycle_count": 100}` |
| `mes/machines/{id}/production` | Publish | `{"quantity": 500, "rejection": 5}` |
| `mes/machines/{id}/breakdown` | Subscribe | `{"type": "mechanical", "code": "E001"}` |
| `mes/machines/{id}/command` | Subscribe | `{"action": "reset", "params": {}}` |

## Machine Data Collection Architecture

```
┌──────────┐  OPC-UA/Modbus  ┌──────────┐   MQTT/HTTP   ┌──────────────┐
│  CNC     │ ──────────────► │  Edge     │ ─────────────► │  MES         │
│  Machine │                 │  Gateway  │                │  Server      │
└──────────┘                 └──────────┘                └──────────────┘
┌──────────┐  Digital I/O    ┌──────────┐   MQTT/HTTP   ┌──────────────┐
│  Manual  │ ──────────────► │  Sensor   │ ─────────────► │  MES         │
│  Machine │                 │  Hub      │                │  Server      │
└──────────┘                 └──────────┘                └──────────────┘
```

### Data Points Collected
- Machine run status (running/idle/breakdown)
- Cycle count (from PLC counter)
- Production quantity
- Cycle time per part
- Error/fault codes
- Power consumption
- Temperature readings

### Notification Channels

```
┌──────────────┐     Queue Job     ┌──────────────┐
│  Event       │ ────────────────► │  Notification│
│  Triggered   │                   │  Service     │
└──────────────┘                   └──────┬───────┘
                                          │
                    ┌─────────────┬───────┼───────┬─────────────┐
                    ▼             ▼       ▼       ▼             ▼
              ┌──────────┐ ┌──────────┐ ┌──────┐ ┌────────┐ ┌────────┐
              │  Email   │ │   SMS    │ │WhatsApp│ │ In-App │ │WebSock │
              │  (SMTP)  │ │  (API)   │ │ (API)  │ │ (DB)   │ │(Pusher)│
              └──────────┘ └──────────┘ └──────┘ └────────┘ └────────┘
```

## Approval Workflow

```
Operator submits production data
         │
         ▼
   ┌─────────────┐
   │  Pending    │
   └──────┬──────┘
          │
    ┌─────▼─────┐
    │ Supervisor│
    │  Review   │
    └─────┬─────┘
          │
    ┌─────▼─────┐
    │  Approve  │────► Record locked, OEE calculated
    └───────────┘
    │
    │  Reject
    └───────────┘
          │
          ▼
    ┌─────────────┐
    │  Revised    │────► Operator corrects and resubmits
    └─────────────┘
```
